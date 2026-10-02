<?php

namespace App\Services\Gdt;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Thin client for the hoadondientu.gdt.gov.vn portal API. TLS verification stays on.
 * Throttled responses (429) are retried with exponential backoff, as the portal rate limits aggressively.
 */
class GdtClient
{
    public const BASE_URL = 'https://hoadondientu.gdt.gov.vn';

    public const PAGE_SIZE = 50;

    private const MAX_ATTEMPTS = 10;

    private const MAX_DELAY_MS = 10000;

    /** Overridable so tests do not sleep. */
    public static int $initialDelayMs = 1000;

    public function __construct(private readonly ?string $token = null) {}

    /** Captcha challenge: ['key' => ..., 'content' => svg markup]. */
    public function captcha(): array
    {
        return $this->json($this->send(fn () => $this->http()->get('/api/captcha')));
    }

    /**
     * Exchange username/password/captcha for a portal token. Credentials are forwarded
     * and never stored or logged by this class.
     */
    public function authenticate(string $username, string $password, string $captchaValue, string $captchaKey): array
    {
        $response = $this->send(fn () => $this->http()->asJson()->post('/api/security-taxpayer/authenticate', [
            'username' => $username,
            'password' => $password,
            'cvalue' => $captchaValue,
            'ckey' => $captchaKey,
        ]), retryOnThrottle: false);

        return $this->json($response);
    }

    public function profile(): array
    {
        return $this->json($this->authed('/api/security-taxpayer/profile'));
    }

    /**
     * One page of invoices.
     *
     * @param  string  $apiBase  `query` (standard) or `sco-query` (invoices from cash registers)
     * @param  string  $direction  `sold` or `purchase`
     * @return array<string, mixed> decoded body: `datas` (rows) and `state` (cursor for the next page)
     */
    public function invoicePage(string $apiBase, string $direction, string $sort, ?string $search, ?string $state): array
    {
        $query = ['sort' => $sort, 'size' => self::PAGE_SIZE];
        if ($search) {
            $query['search'] = $search;
        }
        if ($state) {
            $query['state'] = $state;
        }

        return $this->json($this->authed("/api/{$apiBase}/invoices/{$direction}", $query));
    }

    public function invoiceDetail(string $apiBase, string $nbmst, string $khhdon, string $shdon, string $khmshdon): array
    {
        return $this->json($this->authed("/api/{$apiBase}/invoices/detail", compact('nbmst', 'khhdon', 'shdon', 'khmshdon')));
    }

    /** Raw export-xml body (a zip archive containing the XML and the HTML view). */
    public function exportXml(string $apiBase, string $nbmst, string $khhdon, string $shdon, string $khmshdon): string
    {
        return $this->authed("/api/{$apiBase}/invoices/export-xml", compact('nbmst', 'khhdon', 'shdon', 'khmshdon'))->body();
    }

    private function authed(string $path, array $query = []): Response
    {
        if (! $this->token) {
            throw new GdtUnauthorizedException;
        }

        return $this->send(fn () => $this->http()->withToken($this->token)->get($path, $query));
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl(self::BASE_URL)
            ->withHeaders(GdtBrowserHeaders::make())
            ->timeout(20);
    }

    /**
     * @param  callable(): Response  $request
     */
    private function send(callable $request, bool $retryOnThrottle = true): Response
    {
        $delay = static::$initialDelayMs;

        for ($attempt = 1; $attempt <= self::MAX_ATTEMPTS; $attempt++) {
            try {
                $response = $request();
            } catch (ConnectionException $e) {
                throw new GdtException('Không kết nối được cổng thuế.', 0, $e);
            }

            if ($response->status() === 401) {
                throw new GdtUnauthorizedException;
            }

            if ($response->status() === 429 && $retryOnThrottle && $attempt < self::MAX_ATTEMPTS) {
                usleep($delay * 1000);
                $delay = min($delay * 2, self::MAX_DELAY_MS);

                continue;
            }

            if ($response->failed()) {
                throw new GdtException($this->describeFailure($response), $response->status());
            }

            return $response;
        }

        throw new GdtException('Cổng thuế từ chối quá nhiều yêu cầu, thử lại sau.', 429);
    }

    private function describeFailure(Response $response): string
    {
        $message = $response->json('message');

        return 'Cổng thuế trả lỗi '.$response->status().(is_string($message) && $message !== '' ? ': '.$message : '.');
    }

    private function json(Response $response): array
    {
        $data = $response->json();

        if (! is_array($data)) {
            throw new GdtException('Cổng thuế trả dữ liệu không hợp lệ.', $response->status());
        }

        return $data;
    }
}
