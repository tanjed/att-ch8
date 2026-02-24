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
            // Token Cache Key
            $tokenCacheKey = "platform_token_{$credential->id}";
            $token = Cache::get($tokenCacheKey, $credential->access_token);
            $needsNewToken = empty($token);

            // If we have a token (cached or DB), try it first
            if (!$needsNewToken) {
                if (!$this->evaluateCalendarApi($platform, $token, $credential, $setting)) {
                    return;
                }

                $response = $this->executeAction($setting->platformAction->api_curl_template, $token, $credential);
                if ($response['status'] == 401 || $response['status'] == 403) {
                    $needsNewToken = true;
                    Cache::forget($tokenCacheKey); // Bust the cache on auth failure
                } else {
                    $this->logAction($setting, 'success', json_encode($response['body']));
                    return;
                }
            }

            // If we need a new token (or previous one failed with 401/403)
            if ($needsNewToken) {
                $tokenFetched = false;

                // Attempt to Refresh first if we have a refresh token and template
                if ($platform->refresh_curl_template && $credential->refresh_token) {
                    $refreshCurl = $platform->refresh_curl_template;
                    $refreshCurl = str_replace('[REFRESH_TOKEN]', $credential->refresh_token, $refreshCurl);

                    $jsonResponse = $this->fetchAuthResponse($refreshCurl);

                    if ($jsonResponse && data_get($jsonResponse, $platform->auth_token_key)) {
                        $token = data_get($jsonResponse, $platform->auth_token_key);
                        $credential->access_token = $token;
                        if ($platform->refresh_token_key && data_get($jsonResponse, $platform->refresh_token_key)) {
                            $credential->refresh_token = data_get($jsonResponse, $platform->refresh_token_key);
                        }
                        $credential->save();
                        $tokenFetched = true;
                    }
                }

                // If completely new or refresh failed, do a full Re-Login
                if (!$tokenFetched) {
                    $authCurl = $platform->authentication_curl_template;

                    if (!$authCurl) {
                        $this->logAction($setting, 'failed', 'Platform missing auth curl template');
                        return;
                    }

                    $authCurl = str_replace('[USERNAME]', $credential->username, $authCurl);
                    $authCurl = str_replace('[PASSWORD]', $credential->password, $authCurl);

                    $jsonResponse = $this->fetchAuthResponse($authCurl);

                    if ($jsonResponse && data_get($jsonResponse, $platform->auth_token_key)) {
                        $token = data_get($jsonResponse, $platform->auth_token_key);
                        $credential->access_token = $token;
                        if ($platform->refresh_token_key && data_get($jsonResponse, $platform->refresh_token_key)) {
                            $credential->refresh_token = data_get($jsonResponse, $platform->refresh_token_key);
                        }
                        $credential->save();
                    } else {
                        $this->logAction($setting, 'failed', 'Failed to extract auth token during login. Response: ' . json_encode($jsonResponse));
                        return;
                    }
                }

                // Cache the newly fetched token for 60 minutes
                Cache::put($tokenCacheKey, $token, now()->addMinutes(60));

                // Execute the intermediate related_auth_curl if defined on the platform
                if ($platform->related_auth_curl) {
                    $this->fetchRelatedAuthUrl($token, $platform->related_auth_curl);
                }

                if (!$this->evaluateCalendarApi($platform, $token, $credential, $setting)) {
                    return;
                }

                // Now execute the actual action with the fresh token
                $response = $this->executeAction($setting->platformAction->api_curl_template, $token, $credential);

                $this->logAction($setting, 'success', json_encode($response['body']));
            }

        } catch (\Exception $e) {
            $this->logAction($setting, 'failed', $e->getMessage());
        }
    }

    private function evaluateCalendarApi($platform, $token, $credential, $setting)
    {
        if (empty($platform->calendar_api_curl_template)) {
            return true;
        }

        $today = now()->format('Y-m-d');
        $cacheKey = "calendar_off_day_{$credential->id}_{$today}";

        if (Cache::get($cacheKey) === 'skipped') {
            $this->logAction($setting, 'skipped', 'Skipped due to Calendar API (Holiday/Leave/Weekend)');
            return false;
        }

        if (Cache::get($cacheKey) === 'working') {
            return true;
        }

        try {
            $curl = $platform->calendar_api_curl_template;
            $curl = str_replace('[TOKEN]', $token, $curl);
            $curl = str_replace('[MONTH_START_DATE_URL]', now()->startOfMonth()->format('d%2Fm%2FY'), $curl);
            $curl = str_replace('[MONTH_END_DATE_URL]', now()->endOfMonth()->format('d%2Fm%2FY'), $curl);
            $curl = str_replace('[TODAY_DATE_URL]', now()->format('d%2Fm%2FY'), $curl);

            if (!str_contains($platform->calendar_api_curl_template, '[TOKEN]')) {
                $curl .= " -H 'Authorization: Bearer {$token}'";
            }

            $transformer = new CurlToHttpRequestTransformer();
            $response = $transformer->execute($curl);

            if ($response->successful()) {
                $data = $response->json();
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
                                Cache::put($cacheKey, 'skipped', now()->endOfDay());
                                $this->logAction($setting, 'skipped', "Skipped due to Calendar API matching off-day flag: {$matchingFlag['flag_full_name']}");
                                return false;
                            }
                        }
                    }
                }

                Cache::put($cacheKey, 'working', now()->endOfDay());
                return true;
            }

            if ($response->status() == 401 || $response->status() == 403) {
                // Return true without caching so the main loop can bust the token cache and retry
                return true;
            }

            Log::error('Calendar API failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            // If the Calendar API is down, we default to running the action so users don't miss attendance
            Cache::put($cacheKey, 'working', now()->endOfDay());
            return true;

        } catch (\Exception $e) {
            Log::error('Calendar API exception', ['message' => $e->getMessage()]);
            return true;
        }
    }

    private function fetchAuthResponse($curlCommand)
    {
        try {
            $transformer = new CurlToHttpRequestTransformer();
            $response = $transformer->execute($curlCommand);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Auth request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Auth request exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function fetchRelatedAuthUrl($token, $relatedAuthCurlTemplate)
    {
        try {
            // Replace [TOKEN] dynamically if present in the template, otherwise append the header manually just in case
            $curl = str_replace('[TOKEN]', $token, $relatedAuthCurlTemplate);

            if (!str_contains($relatedAuthCurlTemplate, '[TOKEN]')) {
                $curl .= " -H 'Authorization: Bearer {$token}'";
            }

            $transformer = new CurlToHttpRequestTransformer();
            $response = $transformer->execute($curl);

            if (!$response->successful()) {
                Log::warning('Related auth URL failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Related auth URL exception', ['message' => $e->getMessage()]);
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
