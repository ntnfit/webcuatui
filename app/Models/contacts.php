<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class contacts extends Model
{
    use HasFactory;
    protected $fillable = ['full_name', 'email', 'phone_number', 'topic', 'company', 'message', 'contact_reason_id'];

    public function contactReason() :BelongsTo
    {
        return $this->belongsTo(ContactReason::class, 'contact_reason_id');
    }
}
