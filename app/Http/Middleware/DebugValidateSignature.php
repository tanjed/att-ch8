<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Log;

class DebugValidateSignature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     * @return mixed
     *
     * @throws \Illuminate\Routing\Exceptions\InvalidSignatureException
     */
    public function handle(Request $request, Closure $next)
    {
        // WORKAROUND: Force HTTPS for signature validation in production
        // since the proxy is not correctly forwarding X-Forwarded-Proto
        if (app()->environment('production')) {
            $request->headers->set('X-Forwarded-Proto', 'https');
        }

        $fullUrl = $request->fullUrl();
        $urlWithoutSignature = $this->getUrlWithoutSignature($fullUrl);

        // Extract signature from URL
        $providedSignature = $request->query('signature');
        $expires = $request->query('expires');

        // Compute expected signature
        $expectedSignature = hash_hmac('sha256', $urlWithoutSignature, config('app.key'));

        Log::info('===== SIGNATURE VALIDATION DEBUG =====', [
            'request_method' => $request->method(),
            'request_scheme' => $request->getScheme(),
            'request_host' => $request->getHost(),
            'request_port' => $request->getPort(),
            'request_is_secure' => $request->isSecure(),
            'full_url' => $fullUrl,
            'url_without_signature' => $urlWithoutSignature,
            'provided_signature' => $providedSignature,
            'expected_signature' => $expectedSignature,
            'signatures_match' => hash_equals($expectedSignature, $providedSignature ?? ''),
            'expires' => $expires,
            'is_expired' => $expires ? (time() > $expires) : null,
            'app_key' => config('app.key'),
            'app_url' => config('app.url'),
            'app_env' => app()->environment(),
            'proxy_headers' => [
                'X-Forwarded-Proto' => $request->header('X-Forwarded-Proto'),
                'X-Forwarded-Host' => $request->header('X-Forwarded-Host'),
                'X-Forwarded-For' => $request->header('X-Forwarded-For'),
                'X-Forwarded-Port' => $request->header('X-Forwarded-Port'),
            ],
            'server_vars' => [
                'HTTPS' => $_SERVER['HTTPS'] ?? null,
                'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? null,
                'REQUEST_SCHEME' => $_SERVER['REQUEST_SCHEME'] ?? null,
            ],
        ]);

        // Check expiration
        if ($expires && time() > $expires) {
            Log::error('Signature validation failed: URL expired', [
                'expires' => $expires,
                'current_time' => time(),
            ]);
            throw new InvalidSignatureException;
        }

        // Validate signature
        if (!$providedSignature || !hash_equals($expectedSignature, $providedSignature)) {
            Log::error('Signature validation failed: Signature mismatch', [
                'provided' => $providedSignature,
                'expected' => $expectedSignature,
            ]);
            throw new InvalidSignatureException;
        }

        Log::info('Signature validation PASSED');

        return $next($request);
    }

    /**
     * Get the URL without the signature parameter.
     */
    protected function getUrlWithoutSignature(string $url): string
    {
        $parsed = parse_url($url);

        if (!isset($parsed['query'])) {
            return $url;
        }

        parse_str($parsed['query'], $params);
        unset($params['signature']);

        $newQuery = http_build_query($params);

        $baseUrl = $parsed['scheme'] . '://' . $parsed['host'];
        if (isset($parsed['port'])) {
            $baseUrl .= ':' . $parsed['port'];
        }
        $baseUrl .= $parsed['path'];

        return $newQuery ? $baseUrl . '?' . $newQuery : $baseUrl;
    }
}
