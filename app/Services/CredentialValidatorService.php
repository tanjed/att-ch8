<?php

namespace App\Services;

use App\Models\Platform;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\CurlToHttpRequestTransformer;

class CredentialValidatorService
{
    /**
     * Validate credentials by attempting to fetch an auth token,
     * hitting the related auth URL (if exists), and hitting the calendar API (if exists).
     *
     * @param Platform $platform
     * @param string $username
     * @param string $password
     * @param string|null $location
     * @return array Contains 'success', 'access_token', 'refresh_token', and 'error'
     */
    public function validateAndFetchTokens(Platform $platform, string $username, string $password, ?string $location = null): array
    {
        try {
            // 1. Fetch Tokens via Auth Curl
            $authCurl = $platform->authentication_curl_template;

            if (!$authCurl) {
                return ['success' => false, 'error' => 'Platform missing auth curl template'];
            }

            $authCurl = str_replace('[USERNAME]', $username, $authCurl);
            $authCurl = str_replace('[PASSWORD]', $password, $authCurl);
            $authCurl = str_replace('[UUID]', (string) \Illuminate\Support\Str::uuid(), $authCurl);

            Log::debug("---- TEST CREDENTIALS: Auth cURL ----");
            Log::debug($authCurl);

            $authResponse = $this->fetchAuthResponse($authCurl);

            if (!$authResponse || !data_get($authResponse, $platform->auth_token_key)) {
                return ['success' => false, 'error' => 'Invalid username or password, or failed to parse auth token.'];
            }

            $accessToken = data_get($authResponse, $platform->auth_token_key);
            $refreshToken = $platform->refresh_token_key ? data_get($authResponse, $platform->refresh_token_key) : null;

            // 2. Fetch Related Auth URL (if defined)
            if ($platform->related_auth_curl) {
                sleep(2); // Prevent rapid-fire rate limiting 401s
                $relatedSuccess = $this->fetchRelatedAuthUrl($accessToken, $platform->related_auth_curl);
                if (!$relatedSuccess) {
                    return ['success' => false, 'error' => 'Failed testing the related authentication URL.'];
                }
            }

            // 3. Evaluate Calendar API (if defined)
            if ($platform->calendar_api_curl_template) {
                sleep(2); // Prevent rapid-fire rate limiting 401s
                $calendarSuccess = $this->testCalendarApi($platform->calendar_api_curl_template, $accessToken);
                if (!$calendarSuccess) {
                    return ['success' => false, 'error' => 'Failed fetching data from the calendar API. Token might be invalid or lacking permissions.'];
                }
            }

            return [
                'success' => true,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ];

        } catch (\Exception $e) {
            Log::error('Validation Exception: ' . $e->getMessage());
            return ['success' => false, 'error' => 'An unexpected error occurred during validation: ' . $e->getMessage()];
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

            Log::error('Auth request failed during validation', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Auth request exception during validation', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function fetchRelatedAuthUrl($token, $relatedAuthCurlTemplate): bool
    {
        try {
            $curl = str_replace('[TOKEN]', $token, $relatedAuthCurlTemplate);

            if (!str_contains($relatedAuthCurlTemplate, '[TOKEN]')) {
                $curl .= " -H 'Authorization: Bearer {$token}'";
            }

            Log::debug("---- TEST CREDENTIALS: Related Auth cURL ----");
            Log::debug($curl);

            $transformer = new CurlToHttpRequestTransformer();
            $response = $transformer->execute($curl);

            if (!$response->successful()) {
                Log::warning('Related auth URL failed during validation', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Related auth URL exception during validation', ['message' => $e->getMessage()]);
            return false;
        }
    }

    private function testCalendarApi($calendarCurlTemplate, $token): bool
    {
        try {
            $curl = $calendarCurlTemplate;
            $curl = str_replace('[TOKEN]', $token, $curl);
            $curl = str_replace('[MONTH_START_DATE_URL]', urlencode(now()->startOfMonth()->format('d/m/Y')), $curl);
            $curl = str_replace('[MONTH_END_DATE_URL]', urlencode(now()->endOfMonth()->format('d/m/Y')), $curl);
            $curl = str_replace('[TODAY_DATE_URL]', urlencode(now()->format('d/m/Y')), $curl);

            if (!str_contains($calendarCurlTemplate, '[TOKEN]')) {
                $curl .= " -H 'Authorization: Bearer {$token}'";
            }

            Log::debug("---- TEST CREDENTIALS: Calendar API cURL ----");
            Log::debug($curl);

            $transformer = new CurlToHttpRequestTransformer();
            $response = $transformer->execute($curl);

            if ($response->status() == 401 || $response->status() == 403) {
                Log::error('Calendar validation failed (401/403): ', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Calendar API exception during validation', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
