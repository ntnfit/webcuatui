<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Tag extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'id' => 'integer',
    ];

    public function posts(): BelongsToMany
    {

        return $this->belongsToMany(blogs::class, 'post_tag', 'tag_id', 'post_id');
    }
    public static function getForm():array
    {
        return [
            TextInput::make('name')
                ->live(true)->afterStateUpdated(fn(Set $set, ?string $state) => $set(
                    'slug',
                    Str::slug($state)
                ))
                ->unique('tags', 'name', null, 'id')
                ->required()
                ->maxLength(50),
            TextInput::make('slug')
                ->unique('tags', 'slug', null, 'id')
                ->readOnly()
                ->maxLength(155),
            TextInput::make('name_en')
                ->label('Name (English)')
                ->live(true)->afterStateUpdated(fn(Set $set, ?string $state) => $set(
                    'slug_en',
                    Str::slug($state)
                ))
                ->unique('tags', 'name_en', null, 'id')
                ->required()
                ->maxLength(50),
            TextInput::make('slug_en')
                ->unique('tags', 'slug_en', null, 'id')
                ->readOnly()
                ->maxLength(155),
        ];
    }
    public function getTable()
    {
        return 'tags';
    }
}
