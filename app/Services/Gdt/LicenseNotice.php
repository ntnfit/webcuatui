<?php

namespace App\Services\Gdt;

use App\Models\Company;

/** User-facing texts and banner markup for the Tax license gate. */
final class LicenseNotice
{
    /** Same address as the site footer. */
    public const CONTACT_EMAIL = 'sapb1devbtp@gmail.com';

    /** Banner turns to a warning when this many days or fewer remain. */
    public const WARNING_DAYS = 14;

    public static function blockedMessage(): string
    {
        return 'Gói dùng thử/Tax chưa kích hoạt hoặc đã hết hạn, liên hệ '.self::CONTACT_EMAIL.' để gia hạn.';
    }

    /** Banner html for a company: blocked notice, expiry warning, or a calm status line. */
    public static function bannerHtml(Company $company): string
    {
        if (! $company->hasValidLicense()) {
            $license = $company->activeLicense();
            $since = $license ? ' (hết hạn '.$license->expires_at->format('d/m/Y').')' : '';

            return self::box('#fef2f2', '#b91c1c', e('Giấy phép không hợp lệ. '.self::blockedMessage().$since));
        }

        $license = $company->activeLicense();
        $days = $company->licenseDaysLeft();
        $text = 'Giấy phép hợp lệ, hết hạn ngày '.$license->expires_at->format('d/m/Y').", còn {$days} ngày.";

        if ($days <= self::WARNING_DAYS) {
            return self::box('#fffbeb', '#b45309', e($text.' Liên hệ '.self::CONTACT_EMAIL.' để gia hạn.'));
        }

        return self::box('#f0fdf4', '#15803d', e($text));
    }

    private static function box(string $background, string $color, string $content): string
    {
        return '<div style="margin-bottom:12px;padding:10px 14px;border-radius:8px;background:'.$background.';color:'.$color.'">'.$content.'</div>';
    }
}
