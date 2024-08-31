<?php

namespace App\Models;


use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use Filament\Forms\Set;
use FilamentTiptapEditor\TiptapEditor;
use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Forms\Components\Tabs;
use Illuminate\Support\Str;
class blogs extends Model
{
    use HasFactory;
    protected  $guarded = [];
    protected $casts = [
        'id' => 'integer',
        'published_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'status' => PostStatus::class,
        'user_id' => 'integer',
    ];
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_post', 'post_id', 'category_id');
    }
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class,'post_tag','post_id','tag_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function seoDetail()
    {
        return $this->hasOne(SeoDetail::class,'post_id');
    }
    public function isNotPublished()
    {
        return ! $this->isStatusPublished();
    }

    public function scopePublished(Builder $query)
    {
        return $query->where('status', PostStatus::PUBLISHED)->latest('published_at');
    }

//    public function scopeScheduled(Builder $query)
//    {
//        return $query->where('status', PostStatus::SCHEDULED)->latest('scheduled_for');
//    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', PostStatus::PENDING)->latest('created_at');
    }
    public function formattedPublishedDate()
    {
        return $this->published_at?->format('d M Y');
    }
//    public function isScheduled()
//    {
//        return $this->status === PostStatus::SCHEDULED;
//    }

    public function isStatusPublished()
    {
        return $this->status === PostStatus::PUBLISHED;
    }
    public function relatedPosts($take = 3)
    {
        return $this->whereHas('categories', function ($query) {
            $query->whereIn('categories.id', $this->categories->pluck('id'))
                ->whereNotIn('posts.id', [$this->id]);
        })->published()->with('user')->take($take)->get();
    }
    protected function getFeaturePhotoAttribute()
    {
        return asset('storage/'.$this->cover_photo_path);
    }
    public static function getForm()
    {
        return [
            Section::make('Blog Details')
                ->schema([
                    Fieldset::make('Titles')
                        ->schema([
                            Select::make('category_id')
                                ->multiple()
                                ->preload()
                                ->createOptionForm(Category::getForm())
                                ->searchable()
                                ->relationship('categories', 'name')
                                ->columnSpanFull(),
                            Select::make('tag_id')
                                ->multiple()
                                ->preload()
                                ->createOptionForm(Tag::getForm())
                                ->searchable()
                                ->relationship('tags', 'name')
                                ->columnSpanFull(),
                        ]),

                    Tabs::make('Tabs')
                        ->tabs([
                            Tabs\Tab::make('Vietnamese')
                                ->schema([
                                    TextInput::make('title')
                                        ->live(true)
                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
                                            'slug',
                                            Str::slug($state)
                                        ))
                                        ->required()
                                        ->unique('posts', 'title', null, 'id')
                                        ->maxLength(255),

                                    TextInput::make('slug')
                                        ->maxLength(255),

                                    Textarea::make('sub_title')
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    TiptapEditor::make('body')
                                        ->profile('default')
                                        ->disableFloatingMenus()
                                        ->extraInputAttributes(['style' => 'max-height: 30rem; min-height: 24rem'])
                                        ->required()
                                        ->columnSpanFull(),
                                ]),
                            Tabs\Tab::make('Enghlish')
                                ->schema([
                                    TextInput::make('title_en')
                                        ->label('Title English')
                                        ->live(true)
                                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set(
                                            'slug_en',
                                            Str::slug($state)
                                        ))
                                        ->unique('posts', 'title_en', null, 'id')
                                        ->maxLength(255),

                                    TextInput::make('slug_en')
                                        ->maxLength(255),

                                    Textarea::make('sub_title_en')
                                        ->label('Sub Title English')
                                        ->maxLength(255)
                                        ->columnSpanFull(),
                                    TiptapEditor::make('body_en')
                                        ->label('Body English')
                                        ->profile('default')
                                        ->disableFloatingMenus()
                                        ->extraInputAttributes(['style' => 'max-height: 30rem; min-height: 24rem'])
                                        ->columnSpanFull(),
                                ])
                            ]),
                    Fieldset::make('Feature Image')
                        ->schema([
                            FileUpload::make('cover_photo_path')
                                ->label('Cover Photo')
                                ->directory('/blog-feature-images')
                                ->hint('This cover image is used in your blog post as a feature image. Recommended image size 1200 X 628')
                                ->image()
                                ->preserveFilenames()
                                ->imageEditor()
                                ->maxSize(1024 * 5)
                                ->rules('dimensions:max_width=1920,max_height=1004')
                                ->required(),
                            TextInput::make('photo_alt_text')->required(),
                        ])->columns(1),

                    Fieldset::make('Status')
                        ->schema([

                            ToggleButtons::make('status')
                                ->live()
                                ->inline()
                                ->options(PostStatus::class)
                                ->required(),

//                            DateTimePicker::make('scheduled_for')
//                                ->visible(function ($get) {
//                                    return $get('status') === PostStatus::SCHEDULED->value;
//                                })
//                                ->required(function ($get) {
//                                    return $get('status') === PostStatus::SCHEDULED->value;
//                                })
//                                ->minDate(now()->addMinutes(5))
//                                ->native(false),
                        ]),
                    Select::make('user_id')
                     ->relationship('user', 'name')
                        ->nullable(false)
                        ->default(auth()->id()),

                ]),
        ];
    }
    public function getTable()
    {
        return 'posts';
    }
}
