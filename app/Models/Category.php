<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Category extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(blogs::class,'category_post','category_id','post_id');
    }
    public static function getForm()
    {
        return [
            TextInput::make('name')
                ->live(true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state) {

                    $set('slug', Str::slug($state));
                })
                ->unique('categories', 'name', null, 'id')
                ->required()
                ->maxLength(155),
                TextInput::make('slug')
                    ->unique('categories', 'slug', null, 'id')
                    ->readOnly()
                    ->maxLength(255),
            TextInput::make('name_en')
                ->live(true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $operation, ?string $old, ?string $state) {

                    $set('slug_en', Str::slug($state));
                })
                ->unique('categories', 'name_en', null, 'id')
                ->required()
                ->maxLength(155),
            TextInput::make('slug_en')
                ->unique('categories', 'slug', null, 'id')
                ->readOnly()
                ->maxLength(255),
        ];
    }
}
