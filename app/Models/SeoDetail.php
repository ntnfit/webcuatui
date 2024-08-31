<?php

namespace App\Models;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\blogs as post;
class SeoDetail extends Model
{
    use HasFactory;

    const KEYWORDS = [
        'technology',
        'innovation',
        'science',
        'artificial intelligence',
        'machine learning',
        'data science',
        'coding',
        'programming',
        'web development',
        'cybersecurity',
        'digital marketing',
        'social media',
        'business',
        'finance',
        'health',
        'fitness',
        'travel',
        'food',
        'photography',
        'music',
        'movies',
        'fashion',
        'sports',
        'gaming',
        'books',
        'education',
        'history',
        'culture',
    ];

    protected $fillable = [
        'post_id',
        'title',
        'keywords',
        'description',
        'user_id',
    ];

    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'user_id' => 'integer',
        'keywords' => 'json',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(post::class,'post_id')->orderByDesc('id');
    }

    public static function getForm()
    {
        return [
            Select::make('post_id')
                ->createOptionForm(post::getForm())
                ->editOptionForm(post::getForm())
                ->relationship('post', 'title')
                ->unique('seo_details', 'post_id', null, 'id')
                ->required()
                ->preload()
                ->searchable()
                ->default(request('post_id') ?? '')
                ->columnSpanFull(),
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            TagsInput::make('keywords')
                ->columnSpanFull(),
            Textarea::make('description')
                ->required()
                ->maxLength(65535)
                ->columnSpanFull(),
        ];
    }


    public function getTable()
    {
        return 'seo_details';
    }
}
