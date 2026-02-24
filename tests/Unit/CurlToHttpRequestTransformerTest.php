<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\CurlToHttpRequestTransformer;

class CurlToHttpRequestTransformerTest extends TestCase
{
    private CurlToHttpRequestTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new CurlToHttpRequestTransformer();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_parses_login_curl_command()
    {
        $curl = "curl 'https://sso.pihr.xyz/login/userlogin' \
  -H 'accept: */*' \
  -H 'accept-language: en-US,en;q=0.9' \
  -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundaryXJKOXKDDQI4IoKIv' \
  -H 'origin: https://sso.pihr.xyz' \
  -H 'priority: u=1, i' \
  -H 'referer: https://sso.pihr.xyz/?returnUrl=https://shohoz.pihr.xyz/login' \
  -H 'sec-ch-ua: \"Not)A;Brand\";v=\"8\", \"Chromium\";v=\"138\", \"Google Chrome\";v=\"138\"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: \"Linux\"' \
  -H 'sec-fetch-dest: empty' \
  -H 'sec-fetch-mode: cors' \
  -H 'sec-fetch-site: same-origin' \
  -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36' \
  --data-raw '------WebKitFormBoundaryXJKOXKDDQI4IoKIv\r\nContent-Disposition: form-data; name=\"UserName\"\r\n\r\ntestuser\r\n------WebKitFormBoundaryXJKOXKDDQI4IoKIv\r\nContent-Disposition: form-data; name=\"Password\"\r\n\r\ntestpass\r\n------WebKitFormBoundaryXJKOXKDDQI4IoKIv\r\nContent-Disposition: form-data; name=\"ReturnUrl\"\r\n\r\nhttps://shohoz.pihr.xyz/login\r\n------WebKitFormBoundaryXJKOXKDDQI4IoKIv\r\nContent-Disposition: form-data; name=\"IpAddress\"\r\n\r\n47fb924e-b53c-4ed8-83ea-f05049190d57.local\r\n------WebKitFormBoundaryXJKOXKDDQI4IoKIv--\r\n'";

        $this->transformer->parse($curl);

        $this->assertEquals('https://sso.pihr.xyz/login/userlogin', $this->transformer->getUrl());
        $this->assertEquals('POST', $this->transformer->getMethod());
        $this->assertArrayHasKey('accept', $this->transformer->getHeaders());
        $this->assertArrayHasKey('content-type', $this->transformer->getHeaders());
        $this->assertStringContainsString('WebKitFormBoundary', $this->transformer->getRawBody());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_parses_punch_in_curl_command()
    {
        $curl = "curl 'https://api.pihr.xyz/api/v2/my-attendances/in-time?' \
  -X 'POST' \
  -H 'accept: */*' \
  -H 'accept-language: en-US,en;q=0.9' \
  -H 'apikey: QOV0rgp9-bLqdJV77iPRvSKhFQ5m9YMw' \
  -H 'authorization: Bearer test-token' \
  -H 'content-length: 0' \
  -H 'origin: https://shohoz.pihr.xyz' \
  -H 'priority: u=1, i' \
  -H 'referer: https://shohoz.pihr.xyz/' \
  -H 'sec-ch-ua: \"Not)A;Brand\";v=\"8\", \"Chromium\";v=\"138\", \"Google Chrome\";v=\"138\"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: \"Linux\"' \
  -H 'sec-fetch-dest: empty' \
  -H 'sec-fetch-mode: cors' \
  -H 'sec-fetch-site: same-site' \
  -H 'service: pihr' \
  -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36'";

        $this->transformer->parse($curl);

        $this->assertEquals('https://api.pihr.xyz/api/v2/my-attendances/in-time?', $this->transformer->getUrl());
        $this->assertEquals('POST', $this->transformer->getMethod());
        $this->assertArrayHasKey('authorization', $this->transformer->getHeaders());
        $this->assertArrayHasKey('apikey', $this->transformer->getHeaders());
        $this->assertEquals('', $this->transformer->getRawBody());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_parses_punch_out_curl_command()
    {
        $curl = "curl 'https://api.pihr.xyz/api/v2/my-attendances/out-time?out_time_remarks=' \
  -X 'POST' \
  -H 'accept: */*' \
  -H 'accept-language: en-US,en;q=0.9' \
  -H 'apikey: QOV0rgp9-bLqdJV77iPRvSKhFQ5m9YMw' \
  -H 'authorization: Bearer test-token' \
  -H 'content-length: 0' \
  -H 'origin: https://shohoz.pihr.xyz' \
  -H 'priority: u=1, i' \
  -H 'referer: https://shohoz.pihr.xyz/' \
  -H 'sec-ch-ua: \"Not)A;Brand\";v=\"8\", \"Chromium\";v=\"138\", \"Google Chrome\";v=\"138\"' \
  -H 'sec-ch-ua-mobile: ?0' \
  -H 'sec-ch-ua-platform: \"Linux\"' \
  -H 'sec-fetch-dest: empty' \
  -H 'sec-fetch-mode: cors' \
  -H 'sec-fetch-site: same-site' \
  -H 'service: pihr' \
  -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36'";

        $this->transformer->parse($curl);

        $this->assertEquals('https://api.pihr.xyz/api/v2/my-attendances/out-time?out_time_remarks=', $this->transformer->getUrl());
        $this->assertEquals('POST', $this->transformer->getMethod());
        $this->assertArrayHasKey('authorization', $this->transformer->getHeaders());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_defaults_to_post_when_data_is_present()
    {
        $curl = "curl 'https://example.com/api' --data-raw 'foo=bar'";

        $this->transformer->parse($curl);

        $this->assertEquals('POST', $this->transformer->getMethod());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_converts_literal_rn_to_crlf()
    {
        $curl = "curl 'https://example.com/api' --data-raw 'line1\\r\\nline2'";

        $this->transformer->parse($curl);

        $this->assertEquals("line1\r\nline2", $this->transformer->getRawBody());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_malformed_curl_with_missing_opening_quote()
    {
        // This matches the actual data stored in the database - missing opening quote for --data-raw
        $curl = "curl 'https://sso.pihr.xyz/login/userlogin' -H 'content-type: multipart/form-data; boundary=----WebKitFormBoundaryXJKOXKDDQI4IoKIv' --data-raw ------WebKitFormBoundaryXJKOXKDDQI4IoKIv\\r\\nContent-Disposition: form-data; name=\"UserName\"\\r\\n\\r\\ntestuser\\r\\n------WebKitFormBoundaryXJKOXKDDQI4IoKIv--\\r\\n'";

        $this->transformer->parse($curl);

        $this->assertEquals('https://sso.pihr.xyz/login/userlogin', $this->transformer->getUrl());
        $this->assertEquals('POST', $this->transformer->getMethod());
        $this->assertStringContainsString('UserName', $this->transformer->getRawBody());
        $this->assertStringContainsString('testuser', $this->transformer->getRawBody());
        // Should contain actual CRLF after conversion
        $this->assertStringContainsString("\r\n", $this->transformer->getRawBody());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_makes_actual_api_request_without_405_error()
    {
        $curl = "curl 'https://api.pihr.xyz/api/v2/my-attendances/in-time?' \
  -X 'POST' \
  -H 'accept: */*' \
  -H 'apikey: QOV0rgp9-bLqdJV77iPRvSKhFQ5m9YMw' \
  -H 'authorization: Bearer invalid-token-for-testing' \
  -H 'origin: https://shohoz.pihr.xyz' \
  -H 'service: pihr'";

        $response = $this->transformer->execute($curl);

        // The key assertion: should NOT get 405 Method Not Allowed
        // 401 (Unauthorized) is expected since we used an invalid token
        $this->assertNotEquals(405, $response->status(), 'Should not get 405 Method Not Allowed - this means the transformer is working correctly');
        $this->assertEquals('POST', $this->transformer->getMethod());
        $this->assertEquals('https://api.pihr.xyz/api/v2/my-attendances/in-time?', $this->transformer->getUrl());
    }
}
