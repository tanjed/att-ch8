<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;

class CurlToHttpRequestTransformer
{
    private string $url = '';
    private string $method = 'GET';
    private array $headers = [];
    private array $data = [];
    private bool $isJson = false;
    private bool $isForm = false;

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
        $this->data = [];
        $this->isJson = false;
        $this->isForm = false;

        // Clean up the command
        $curlCommand = trim(preg_replace('/\s+/', ' ', $curlCommand));
        $curlCommand = str_replace(["\\\r\n", "\\\n", "\\\r", "\\\t"], ' ', $curlCommand);

        // Remove 'curl' prefix
        $curlCommand = preg_replace('/^curl\s+/i', '', $curlCommand);

        // Parse URL
        if (preg_match('/^["\']?(https?:\/\/[^\s\'"]+)["\']?/', $curlCommand, $matches)) {
            $this->url = $matches[1];
        } elseif (preg_match('/--location\s+["\']?([^\s\'"]+)["\']?/', $curlCommand, $matches)) {
            $this->url = $matches[1];
        } elseif (preg_match('/(?:^|\s)(https?:\/\/[^\s]+)/', $curlCommand, $matches)) {
            $this->url = $matches[1];
        }

        // Parse method
        if (preg_match('/-X\s+["\']?([A-Z]+)["\']?/i', $curlCommand, $matches)) {
            $this->method = strtoupper($matches[1]);
        } elseif (preg_match('/--request\s+["\']?([A-Z]+)["\']?/i', $curlCommand, $matches)) {
            $this->method = strtoupper($matches[1]);
        }

        // Parse headers
        preg_match_all('/-H\s+["\']([^"\']+)["\']/', $curlCommand, $headerMatches);
        foreach ($headerMatches[1] as $header) {
            $parts = explode(':', $header, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $this->headers[$key] = $value;

                // Detect content type
                if (strtolower($key) === 'content-type') {
                    if (str_contains(strtolower($value), 'application/json')) {
                        $this->isJson = true;
                    } elseif (str_contains(strtolower($value), 'x-www-form-urlencoded')) {
                        $this->isForm = true;
                    }
                }
            }
        }

        // Parse data
        // Try --data-raw first (common in Postman exports)
        if (preg_match('/--data-raw\s+["\'](.+?)["\'](?=\s+-|\s*$)/s', $curlCommand, $matches)) {
            $this->parseData($matches[1]);
        }
        // Try --data
        elseif (preg_match('/--data\s+["\'](.+?)["\'](?=\s+-|\s*$)/s', $curlCommand, $matches)) {
            $this->parseData($matches[1]);
        }
        // Try -d
        elseif (preg_match('/-d\s+["\'](.+?)["\'](?=\s+-|\s*$)/s', $curlCommand, $matches)) {
            $this->parseData($matches[1]);
        }
        // Try --data-binary
        elseif (preg_match('/--data-binary\s+["\'](.+?)["\'](?=\s+-|\s*$)/s', $curlCommand, $matches)) {
            $this->parseData($matches[1]);
        }

        // If no Content-Type header was found but we have JSON data, assume JSON
        if (!empty($this->data) && !$this->isForm && !$this->isJson) {
            $this->isJson = true;
        }
    }

    /**
     * Parse data payload.
     *
     * @param string $data
     * @return void
     */
    private function parseData(string $data): void
    {
        $data = trim($data);

        // Try to decode as JSON first
        $decoded = json_decode($data, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->data = $decoded;
            $this->isJson = true;
            return;
        }

        // Try to parse as form-urlencoded
        parse_str($data, $parsed);
        if (!empty($parsed)) {
            $this->data = $parsed;
            $this->isForm = true;
            return;
        }

        // Store as raw data
        $this->data = ['raw' => $data];
    }

    /**
     * Make the HTTP request using Laravel HTTP client.
     *
     * @return Response
     */
    public function makeRequest(): Response
    {
        $http = Http::withHeaders($this->headers);

        $method = strtolower($this->method);
        $url = $this->url;

        // Remove Content-Type from headers as Laravel handles it
        $headersWithoutContentType = array_filter(
            $this->headers,
            fn($key) => strtolower($key) !== 'content-type',
            ARRAY_FILTER_USE_KEY
        );
        $http = Http::withHeaders($headersWithoutContentType);

        if ($this->isJson && !empty($this->data)) {
            $http = Http::withHeaders($this->headers);
            unset($headersWithoutContentType['Content-Type'], $headersWithoutContentType['content-type']);
            $http = Http::withHeaders($headersWithoutContentType);
            return $http->withBody(json_encode($this->data), 'application/json')->send($method, $url);
        }

        if ($this->isForm && !empty($this->data)) {
            return match ($method) {
                'post' => $http->asForm()->post($url, $this->data),
                'put' => $http->asForm()->put($url, $this->data),
                'patch' => $http->asForm()->patch($url, $this->data),
                default => $http->asForm()->send($method, $url, ['form_params' => $this->data]),
            };
        }

        return match ($method) {
            'get' => $http->get($url, $this->data),
            'post' => $http->post($url, $this->data),
            'put' => $http->put($url, $this->data),
            'patch' => $http->patch($url, $this->data),
            'delete' => $http->delete($url, $this->data),
            default => $http->send($method, $url),
        };
    }

    /**
     * Get parsed URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Get parsed method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get parsed headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get parsed data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
