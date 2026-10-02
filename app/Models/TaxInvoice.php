<?php

namespace App\Models;

use App\Services\Gdt\CompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(CompanyScope::class)]
class TaxInvoice extends Model
{
    use HasFactory;

    public const DIRECTION_SOLD = 'sold';

    public const DIRECTION_PURCHASE = 'purchase';

    public const SOURCE_STANDARD = 'standard';

    public const SOURCE_MTT = 'mtt';

    protected $fillable = [
        'company_id', 'direction', 'source', 'mst_seller', 'mst_buyer', 'seller_name', 'buyer_name',
        'number', 'symbol', 'template', 'issued_at', 'total_before_tax', 'total_tax', 'total_payment',
        'currency', 'status', 'check_status', 'raw', 'detail',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'total_before_tax' => 'decimal:2',
            'total_tax' => 'decimal:2',
            'total_payment' => 'decimal:2',
            'raw' => 'array',
            'detail' => 'array',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /** Portal API prefix for this invoice: MTT invoices live under sco-query. */
    public function apiBase(): string
    {
        return $this->source === self::SOURCE_MTT ? 'sco-query' : 'query';
    }

    /** Invoice payload used by exports: detail (with line items) wins over the list row. */
    public function payload(): array
    {
        return $this->detail ?: $this->raw;
    }
}
