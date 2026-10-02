<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const TYPE_PHYSICAL = 'physical';

    public const TYPE_ADDON = 'addon';

    public const BILLING_ONE_TIME = 'one_time';

    public const BILLING_YEARLY = 'yearly';

    public const BILLING_QUOTE = 'quote';

    /** Integration key => Vietnamese label shown on the marketplace. */
    public const INTEGRATIONS = [
        'VAS' => 'VAS (báo cáo kế toán)',
        'EInvoice' => 'Hóa đơn điện tử',
        'Bank' => 'Ngân hàng',
        'SePay' => 'SePay',
        'Magento' => 'Magento',
        'Shopify' => 'Shopify',
    ];

    public const SAP_VERSIONS = ['9.3', '10.0'];

    public const DB_SUPPORT = [
        'sqlserver' => 'SQL Server',
        'hana' => 'SAP HANA',
    ];

    public const BILLING_OPTIONS = [
        self::BILLING_ONE_TIME => 'Mua một lần',
        self::BILLING_YEARLY => 'Theo năm',
        self::BILLING_QUOTE => 'Báo giá',
    ];

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'sap_versions' => 'array',
            'db_support' => 'array',
            'features' => 'array',
            'data_flow' => 'array',
            'faqs' => 'array',
            'gallery' => 'array',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopeAddons(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_ADDON);
    }

    public function scopeForIntegration(Builder $query, ?string $integration): Builder
    {
        return $integration && array_key_exists($integration, self::INTEGRATIONS)
            ? $query->where('integration', $integration)
            : $query;
    }

    public function scopeForSapVersion(Builder $query, ?string $version): Builder
    {
        return $version && in_array($version, self::SAP_VERSIONS, true)
            ? $query->whereJsonContains('sap_versions', $version)
            : $query;
    }

    public function isAddon(): bool
    {
        return $this->type === self::TYPE_ADDON;
    }

    public function isQuoteBased(): bool
    {
        return $this->billing === self::BILLING_QUOTE;
    }

    public function integrationLabel(): ?string
    {
        return self::INTEGRATIONS[$this->integration] ?? $this->integration;
    }

    /** Short plain-text blurb for cards and meta descriptions. */
    public function blurb(): string
    {
        return $this->summary ?: str($this->description)->limit(160)->toString();
    }

    /** Public URL of the product page, depending on its type. */
    public function publicUrl(): string
    {
        return $this->isAddon()
            ? route('marketplace.show', $this->slug)
            : route('shop.show', $this->slug);
    }
}
