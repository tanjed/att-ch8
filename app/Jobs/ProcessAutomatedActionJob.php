<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\UserActionSetting;
use App\Models\ActionLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\AttendanceSubmitted;
use App\Services\CurlToHttpRequestTransformer;
use Log;

class ProcessAutomatedActionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $settingId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($settingId)
    {
        $this->settingId = $settingId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $setting = UserActionSetting::with(['user.credentials', 'platformAction.platform'])->find($this->settingId);

        if (!$setting || !$setting->is_active) {
            return;
        }

        $this->processSetting($setting);
    }

    private function processSetting($setting)
    {
        $user = $setting->user;
        $platform = $setting->platformAction->platform;

        $credential = $user->credentials->where('platform_id', $platform->id)->first();

        if (!$credential) {
            $this->logAction($setting, 'failed', 'Missing platform credentials');
            return;
        }

        try {
            $maxAttempts = 3; // 1 initial try + up to 2 retries on 401/403

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                // Token Cache Key
                $tokenCacheKey = "platform_token_{$credential->id}";
                $token = Cache::get($tokenCacheKey, $credential->access_token);
                $needsNewToken = empty($token);

                // 1. Fetch new token if needed
                if ($needsNewToken) {
                    $validator = new \App\Services\CredentialValidatorService();
                    $validationResult = $validator->validateAndFetchTokens($platform, $credential->username, $credential->password, $credential->location);
                    
                    if (!$validationResult['success']) {
                        if ($attempt >= $maxAttempts) {
                            $this->logAction($setting, 'failed', 'Failed to acquire valid tokens. ' . $validationResult['error']);
                            return;
                        }
                        continue;
                    }

                    $token = $validationResult['access_token'];
                    
                    // Save the new tokens back to the DB credential record
                    $credential->access_token = $token;
                    if (!empty($validationResult['refresh_token'])) {
                        $credential->refresh_token = $validationResult['refresh_token'];
                    }
                    $credential->save();

                    // Cache the newly fetched token for 60 minutes
                    Cache::put($tokenCacheKey, $token, now()->addMinutes(60));
                }

                // 2. Evaluate Calendar API
                if ($platform->calendar_api_curl_template) {
                    $calendarCurl = $platform->calendar_api_curl_template;
                    $calendarCurl = str_replace('[TOKEN]', $token, $calendarCurl);
                    $calendarCurl = str_replace('[MONTH_START_DATE_URL]', urlencode(now()->startOfMonth()->format('d/m/Y')), $calendarCurl);
                    $calendarCurl = str_replace('[MONTH_END_DATE_URL]', urlencode(now()->endOfMonth()->format('d/m/Y')), $calendarCurl);
                    $calendarCurl = str_replace('[TODAY_DATE_URL]', urlencode(now()->format('d/m/Y')), $calendarCurl);

                    if (!str_contains($platform->calendar_api_curl_template, '[TOKEN]')) {
                        $calendarCurl .= " -H 'Authorization: Bearer {$token}'";
                    }

                    try {
                        $transformer = new CurlToHttpRequestTransformer();
                        $calendarResponse = $transformer->execute($calendarCurl);

                        if ($calendarResponse->status() == 401 || $calendarResponse->status() == 403) {
                            Cache::forget($tokenCacheKey);
                            $credential->access_token = null;
                            if ($attempt >= $maxAttempts) {
                                $this->logAction($setting, 'failed', 'Calendar API Auth Failed: ' . json_encode($calendarResponse->json() ?? $calendarResponse->body()));
                                return;
                            }
                            continue; // Retry with new token
                        }

                        if ($calendarResponse->successful()) {
                            $data = $calendarResponse->json();
                            if (isset($data['calendar']) && isset($data['flags'])) {
                                $todayFormatted = now()->format('d/m/Y');
                                $todayColor = $data['calendar'][$todayFormatted] ?? null;

                                if ($todayColor) {
                                    $flags = collect($data['flags']);
                                    $matchingFlag = $flags->firstWhere('color', $todayColor);

                                    if ($matchingFlag) {
                                        $flagCode = $matchingFlag['flag'] ?? '';
                                        // H: Holiday, L: Leave, W: Weekend, A: Absent
                                        if (in_array(strtoupper($flagCode), ['H', 'L', 'W', 'A'])) {
                                            $this->logAction($setting, 'skipped', "Skipped due to Calendar API matching off-day flag: {$matchingFlag['flag_full_name']}");
                                            return;
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Calendar API exception in Job', ['message' => $e->getMessage()]);
                        // If network error, we just proceed with normal action
                    }
                }

                // 3. Execute the actual action
                $response = $this->executeAction($setting->platformAction->api_curl_template, $token, $credential);

                if ($response['status'] == 401 || $response['status'] == 403) {
                    Cache::forget($tokenCacheKey); // Bust the cache on auth failure
                    $credential->access_token = null;

                    if ($attempt >= $maxAttempts) {
                        $this->logAction($setting, 'failed', json_encode($response['body']));
                        return;
                    }
                    continue; // Loop again, fetch new token
                }

                $status = ($response['status'] >= 200 && $response['status'] < 300) ? 'success' : 'failed';
                $this->logAction($setting, $status, json_encode($response['body']));
                return;
            }

        } catch (\Exception $e) {
            $this->logAction($setting, 'failed', $e->getMessage());
        }
    }

    private function executeAction($curlTemplate, $token, $credential)
    {
        $curl = str_replace('[TOKEN]', $token, $curlTemplate);
        if ($credential->location) {
            $parts = explode(',', $credential->location);
            if (count($parts) >= 2) {
                $lat = trim($parts[0]);
                $lng = trim($parts[1]);
                $curl = str_replace('{lat}', $lat, $curl);
                $curl = str_replace('{lng}', $lng, $curl);
            }
        }

        if (!str_contains($curlTemplate, '[TOKEN]')) {
            $curl .= " -H 'Authorization: Bearer {$token}'";
        }

        try {
            $transformer = new CurlToHttpRequestTransformer();
            $response = $transformer->execute($curl);

            return [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Action request exception', ['message' => $e->getMessage()]);
            return [
                'status' => 500,
                'body' => ['error' => $e->getMessage()],
            ];
        }
    }

    private function logAction($setting, $status, $response)
    {
        ActionLog::create([
            'user_id' => $setting->user_id,
            'platform_action_id' => $setting->platform_action_id,
            'status' => $status,
            'response' => $response,
            'executed_at' => now(),
        ]);
        // 4. Set the cache for the rest of the day to signify completion
        $cacheKey = "action_executed_{$setting->id}_" . now()->format('Y-m-d');
        if ($status === 'success' || $status === 'skipped') {
            Cache::put($cacheKey, 'completed', now()->endOfDay());
        } else {
            // Optional: You could remove the cache lock if it fails so it tries again later today
            // Cache::forget($cacheKey);
            Cache::put($cacheKey, 'failed', now()->endOfDay());
        }

        try {
            Mail::to($setting->user->email)->send(new AttendanceSubmitted($setting, $status, $response));
        } catch (\Exception $e) {
            info("Failed to send email to {$setting->user->email}: " . $e->getMessage());
        }

        // Lastly, compute the new `next_execution_time` for tomorrow's run.
        if ($setting->buffer_minutes > 0) {
            $randomOffset = rand(-$setting->buffer_minutes, $setting->buffer_minutes);
            $newTime = \Carbon\Carbon::parse($setting->target_time)->addMinutes($randomOffset)->format('H:i:s');
        } else {
            $newTime = $setting->target_time;
        }

        $setting->update([
            'next_execution_time' => $newTime
        ]);
    }
}
