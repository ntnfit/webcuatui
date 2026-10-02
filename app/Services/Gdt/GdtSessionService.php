<?php

namespace App\Services\Gdt;

use App\Models\Company;
use App\Models\GdtSession;
use Illuminate\Support\Carbon;

/**
 * Owns the per-company GDT token lifecycle. Only the token and its expiry are stored;
 * the portal username and password pass through `connect()` and are discarded.
 */
class GdtSessionService
{
    /** Used when the token carries no readable `exp` claim. */
    private const FALLBACK_TTL_MINUTES = 55;

    public function captcha(): array
    {
        return (new GdtClient)->captcha();
    }

    public function connect(Company $company, string $username, string $password, string $captchaValue, string $captchaKey): GdtSession
    {
        $data = (new GdtClient)->authenticate($username, $password, $captchaValue, $captchaKey);
        $token = $data['token'] ?? null;

        if (! is_string($token) || $token === '') {
            throw new GdtException('Cổng thuế không trả về token đăng nhập.');
        }

        return GdtSession::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->getKey()],
            ['token' => $token, 'expires_at' => $this->expiryOf($token)],
        );
    }

    public function disconnect(Company $company): void
    {
        GdtSession::withoutGlobalScopes()->where('company_id', $company->getKey())->delete();
    }

    public function activeSession(Company $company): ?GdtSession
    {
        $session = GdtSession::withoutGlobalScopes()->where('company_id', $company->getKey())->first();

        return $session?->isActive() ? $session : null;
    }

    /** Client bound to the company's token; throws when the company is not connected. */
    public function clientFor(Company $company): GdtClient
    {
        $session = $this->activeSession($company);

        if (! $session) {
            throw new GdtUnauthorizedException('Công ty chưa kết nối cổng thuế hoặc phiên đã hết hạn.');
        }

        return new GdtClient($session->token);
    }

    /**
     * Run a portal call; a 401 drops the stored token so the UI asks for a new login.
     *
     * @template T
     *
     * @param  callable(GdtClient): T  $callback
     * @return T
     */
    public function run(Company $company, callable $callback): mixed
    {
        try {
            return $callback($this->clientFor($company));
        } catch (GdtUnauthorizedException $e) {
            $this->disconnect($company);

            throw $e;
        }
    }

    private function expiryOf(string $token): Carbon
    {
        $parts = explode('.', $token);

        if (count($parts) === 3) {
            $payload = json_decode((string) base64_decode(strtr($parts[1], '-_', '+/'), false), true);

            if (is_array($payload) && isset($payload['exp']) && is_numeric($payload['exp'])) {
                return Carbon::createFromTimestamp((int) $payload['exp']);
            }
        }

        return now()->addMinutes(self::FALLBACK_TTL_MINUTES);
    }
}
