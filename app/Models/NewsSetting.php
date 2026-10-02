<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Key/value override of config/news.php. Secret rows hold Laravel-encrypted values. */
class NewsSetting extends Model
{
    protected $guarded = [];

    protected $hidden = ['value'];

    protected function casts(): array
    {
        return ['is_secret' => 'boolean'];
    }
}
