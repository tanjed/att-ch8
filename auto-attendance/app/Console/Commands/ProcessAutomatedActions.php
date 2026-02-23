<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessAutomatedActions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-automated-actions';
    protected $description = 'Process automated HR platform actions for the current minute';

    public function handle()
    {
        $now = \Carbon\Carbon::now();
        $currentTime = $now->format('H:i');

        $this->info("Processing actions for time: {$currentTime}");

        // Find active settings matching the current minute (ignoring seconds)
        // using LIKE format to match starting H:i part of the H:i:s
        $settings = \App\Models\UserActionSetting::with(['user.credentials', 'platformAction.platform'])
            ->where('is_active', true)
            ->where('target_time', 'LIKE', $currentTime . '%')
            ->get();

        if ($settings->isEmpty()) {
            $this->info("No actions scheduled for this time.");
            return;
        }

        foreach ($settings as $setting) {
            $this->processSetting($setting);
        }
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
            $token = $credential->access_token;
            $needsNewToken = empty($token);

            // If we have a cached token, try it first
            if (!$needsNewToken) {
                $response = $this->executeAction($setting->platformAction->api_curl_template, $token, $credential);
                if ($response['status'] == 401 || $response['status'] == 403) {
                    $needsNewToken = true;
                } else {
                    $this->logAction($setting, 'success', json_encode($response['body']));
                    if ($setting->teams_webhook_url) {
                        $this->notifyTeams($setting, $response['body']);
                    }
                    return;
                }
            }

            // If we need a new token (or previous one failed with 401)
            if ($needsNewToken) {
                $tokenFetched = false;

                // Attempt to Refresh first if we have a refresh token and template
                if ($platform->refresh_curl_template && $credential->refresh_token) {
                    $refreshCurl = $platform->refresh_curl_template;
                    $refreshCurl = str_replace('[REFRESH_TOKEN]', $credential->refresh_token, $refreshCurl);
                    $refreshCurl = trim(preg_replace('/\s+/', ' ', str_replace(["\\\r\n", "\\\n", "\\\r", "\\\t", "\\"], [' ', ' ', ' ', ' ', ''], $refreshCurl)));

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
                    $authCurl = trim(preg_replace('/\s+/', ' ', str_replace(["\\\r\n", "\\\n", "\\\r", "\\\t", "\\"], [' ', ' ', ' ', ' ', ''], $authCurl)));

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

                // Now execute the actual action with the fresh token
                $response = $this->executeAction($setting->platformAction->api_curl_template, $token, $credential);

                $this->logAction($setting, 'success', json_encode($response['body']));
                if ($setting->teams_webhook_url) {
                    $this->notifyTeams($setting, $response['body']);
                }
            }

        } catch (\Exception $e) {
            $this->logAction($setting, 'failed', $e->getMessage());
        }
    }

    private function fetchAuthResponse($curlCommand)
    {
        $output = shell_exec($curlCommand . ' -s');
        if (!$output)
            return null;
        return json_decode($output, true);
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

        // Clean up formatting
        $curl = trim(preg_replace('/\s+/', ' ', str_replace(["\\\r\n", "\\\n", "\\\r", "\\\t", "\\"], [' ', ' ', ' ', ' ', ''], $curl)));

        // execute and append HTTP status code
        $output = shell_exec($curl . ' -s -w "%{http_code}"');

        $httpCode = (int) substr($output, -3);
        $bodyRaw = substr($output, 0, -3);
        $body = json_decode($bodyRaw, true) ?? $bodyRaw;

        return [
            'status' => $httpCode,
            'body' => $body
        ];
    }

    private function logAction($setting, $status, $response)
    {
        \App\Models\ActionLog::create([
            'user_id' => $setting->user_id,
            'platform_action_id' => $setting->platform_action_id,
            'status' => $status,
            'response' => $response,
            'executed_at' => now(),
        ]);
        $this->info("User {$setting->user_id} Action {$setting->platform_action_id}: {$status}");

        try {
            \Illuminate\Support\Facades\Mail::to($setting->user->email)->send(new \App\Mail\AttendanceSubmitted($setting, $status, $response));
        } catch (\Exception $e) {
            $this->error("Failed to send email to {$setting->user->email}: " . $e->getMessage());
        }
    }

    private function notifyTeams($setting, $response)
    {
        $statusText = is_array($response) ? json_encode($response) : $response;
        $message = [
            "text" => "Automated Action `{$setting->platformAction->name}` executed for {$setting->user->name}.\nResponse: " . substr($statusText, 0, 200)
        ];

        \Illuminate\Support\Facades\Http::post($setting->teams_webhook_url, $message);
    }
}
