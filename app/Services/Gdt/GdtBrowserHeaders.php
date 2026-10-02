<?php

namespace App\Services\Gdt;

use Illuminate\Support\Str;

/**
 * Browser-identical headers for every request to hoadondientu.gdt.gov.vn.
 *
 * The portal sits behind a WAF that scores requests by header set; a default HTTP-client
 * user agent is flagged as a bot and blocked at login. The set below mirrors the portal SPA
 * running in Edge. Update USER_AGENT when it drifts too far from a current browser build.
 */
final class GdtBrowserHeaders
{
    public const ORIGIN = 'https://hoadondientu.gdt.gov.vn';

    public const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) '
        .'Chrome/153.0.0.0 Safari/537.36 Edg/153.0.0.0';

    private const SEC_CH_UA = '"Microsoft Edge";v="153", "Not_A Brand";v="8", "Chromium";v="153"';

    /**
     * @return array<string, string>
     */
    public static function make(): array
    {
        return [
            'accept' => 'application/json, text/plain, */*',
            'accept-language' => 'en-US,en;q=0.9',
            'user-agent' => self::USER_AGENT,
            'origin' => self::ORIGIN,
            'referer' => self::ORIGIN.'/',
            'dnt' => '1',
            'priority' => 'u=1, i',
            'sec-ch-ua' => self::SEC_CH_UA,
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"Windows"',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            // The SPA generates a new id per call; a constant value would expose a non-browser client.
            'request-id' => (string) Str::uuid(),
            'end-point' => '/',
        ];
    }
}
