<?php

namespace App\Models;

use App\Services\Gdt\CompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ScopedBy(CompanyScope::class)]
class TaxExportRun extends Model
{
    use HasFactory;

    public const TYPE_XML_ZIP = 'xml_zip';

    public const TYPE_EXCEL = 'excel';

    public const STATUS_PENDING = 'pending';

    public const STATUS_RUNNING = 'running';

    public const STATUS_DONE = 'done';

    public const STATUS_FAILED = 'failed';

    protected $fillable = ['company_id', 'customer_id', 'type', 'filters', 'status', 'file_path', 'error', 'expires_at'];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function isDownloadable(): bool
    {
        return $this->status === self::STATUS_DONE
            && $this->file_path !== null
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }
}
