<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;

class CurlToHttpRequestTransformer
{
    private string $url = '';
    private string $method = 'GET';
    private array $headers = [];
    private string $rawBody = '';
    private ?string $contentType = null;

    /**
     * Parse a curl command and execute it using Laravel HTTP client.
     *
     * @param string $curlCommand
     * @return Response
     */
    public function execute(string $curlCommand): Response
    {
        $this->parse($curlCommand);
        return $this->makeRequest();
    }

    /**
     * Parse curl command and extract components.
     *
     * @param string $curlCommand
     * @return void
     */
    public function parse(string $curlCommand): void
    {
        // Reset state
        $this->url = '';
        $this->method = 'GET';
        $this->headers = [];
        $this->rawBody = '';
        $this->contentType = null;

        Log::debug('CurlToHttpRequest: Original command', ['command' => $curlCommand]);

        // Normalize line endings and whitespace for parsing
        $curlCommand = str_replace(["\r\n", "\r", "\n"], ' ', $curlCommand);

        // Remove 'curl' prefix
        $curlCommand = preg_replace('/^curl\s+/i', '', $curlCommand);

        Log::debug('CurlToHttpRequest: After cleanup', ['command' => $curlCommand]);

        // Parse URL - look for quoted or unquoted URL at start or after options
        if (preg_match('/^["\']([^"\']+)["\']/', $curlCommand, $matches)) {
            $this->url = $matches[1];
        } elseif (preg_match('/^([^\s]+)/', $curlCommand, $matches)) {
            $this->url = $matches[1];
        }

        // Parse method
        if (preg_match('/-X\s+["\']?([A-Z]+)["\']?/i', $curlCommand, $matches)) {
            $this->method = strtoupper($matches[1]);
        } elseif (preg_match('/--request\s+["\']?([A-Z]+)["\']?/i', $curlCommand, $matches)) {
            $this->method = strtoupper($matches[1]);
        }

        // Parse headers - handle both single and double quotes
        preg_match_all('/-H\s+\'([^\']+)\'/', $curlCommand, $headerMatchesSingle);
        preg_match_all('/-H\s+"([^"]+)"/', $curlCommand, $headerMatchesDouble);

        $allHeaders = array_merge($headerMatchesSingle[1], $headerMatchesDouble[1]);

        foreach ($allHeaders as $header) {
            $parts = explode(':', $header, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $this->headers[$key] = $value;

                // Detect content type
                if (strtolower($key) === 'content-type') {
                    $this->contentType = $value;
                }
            }
        }

        // Parse data - try different flags
        $dataPatterns = [
            '/--data-raw\s+\'([^\']+)\'/s',
            '/--data-raw\s+"([^"]+)"/s',
            '/--data\s+\'([^\']+)\'/s',
            '/--data\s+"([^"]+)"/s',
            '/-d\s+\'([^\']+)\'/s',
            '/-d\s+"([^"]+)"/s',
            '/--data-binary\s+\'([^\']+)\'/s',
            '/--data-binary\s+"([^"]+)"/s',
        ];

        foreach ($dataPatterns as $pattern) {
            if (preg_match($pattern, $curlCommand, $matches)) {
                $this->rawBody = $matches[1];
                break;
            }
        }

        // If data is present but no method was specified, default to POST
        if (!empty($this->rawBody) && $this->method === 'GET') {
            $this->method = 'POST';
        }

        // Convert literal \r\n to actual CRLF for multipart data
        if (str_contains($this->rawBody, '\\r\\n')) {
            $this->rawBody = str_replace('\\r\\n', "\r\n", $this->rawBody);
        }

        Log::debug('CurlToHttpRequest: Parsed result', [
            'url' => $this->url,
            'method' => $this->method,
            'headers' => $this->headers,
            'body_length' => strlen($this->rawBody),
        ]);
    }

    /**
     * Make the HTTP request using Laravel HTTP client.
     *
     * @return Response
     */
    public function makeRequest(): Response
    {
        $method = strtolower($this->method);
        $url = $this->url;

        // Build HTTP client with headers (excluding Content-Type which we handle separately)
        $headersToSend = $this->headers;
        unset($headersToSend['Content-Type'], $headersToSend['content-type']);

        $http = Http::withHeaders($headersToSend)
            ->withoutVerifying() // Match curl's default behavior for development
            ->maxRedirects(10);   // Follow redirects like curl

        // If we have a raw body, send it with the appropriate content type
        if (!empty($this->rawBody)) {
            $contentType = $this->contentType ?? 'application/octet-stream';
            return $http->withBody($this->rawBody, $contentType)->send($method, $url);
        }

        // No body - simple request
        return match ($method) {
            'get' => $http->get($url),
            'post' => $http->post($url),
            'put' => $http->put($url),
            'patch' => $http->patch($url),
            'delete' => $http->delete($url),
            'head' => $http->head($url),
            default => $http->send($method, $url),
        };
    }

    /**
     * Get parsed URL.
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get parsed method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get parsed headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get raw body.
     */
    public function getRawBody(): string
    {
        return $this->rawBody;
    }
}
