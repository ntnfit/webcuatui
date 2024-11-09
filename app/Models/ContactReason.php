<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contacts;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ContactReason extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function contacts():HasMany
    {
        return $this->hasMany(Contacts::class, 'contact_reason_id');
    }
}
