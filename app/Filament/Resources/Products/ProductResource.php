<?php

namespace App\Filament\Resources\Products;

use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Models\Product;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'E-commerce Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Product')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Chung')
                            ->schema([
                                Select::make('type')
                                    ->label('Loại')
                                    ->options([
                                        Product::TYPE_PHYSICAL => 'Hàng hóa (shop)',
                                        Product::TYPE_ADDON => 'Addon SAP B1 (marketplace)',
                                    ])
                                    ->default(Product::TYPE_PHYSICAL)
                                    ->required()
                                    ->live(),
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, Get $get, ?string $state) => filled($get('slug')) ? null : $set('slug', Str::slug((string) $state))),
                                TextInput::make('slug')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Textarea::make('description')
                                    ->rows(8)
                                    ->helperText('Tách đoạn bằng một dòng trống. Hiển thị ở phần tổng quan của trang addon.')
                                    ->columnSpanFull(),
                                TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->default(0)
                                    ->prefix('₫')
                                    ->helperText('Addon báo giá để 0, giá sẽ không được công khai.'),
                                TextInput::make('sale_price')
                                    ->numeric(),
                                TextInput::make('quantity')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                FileUpload::make('image')
                                    ->image(),
                                TextInput::make('status')
                                    ->required()
                                    ->maxLength(255)
                                    ->default('active')
                                    ->helperText('active = hiển thị công khai.'),
                                TextInput::make('category_id')
                                    ->numeric(),
                            ])
                            ->columns(2),

                        Tab::make('Addon')
                            ->visible(fn (Get $get): bool => $get('type') === Product::TYPE_ADDON)
                            ->schema([
                                Select::make('integration')
                                    ->label('Tích hợp')
                                    ->options(Product::INTEGRATIONS)
                                    ->required(fn (Get $get): bool => $get('type') === Product::TYPE_ADDON),
                                Select::make('billing')
                                    ->label('Hình thức bán')
                                    ->options(Product::BILLING_OPTIONS)
                                    ->default(Product::BILLING_QUOTE)
                                    ->required(),
                                CheckboxList::make('sap_versions')
                                    ->label('Phiên bản SAP B1 hỗ trợ')
                                    ->options(array_combine(Product::SAP_VERSIONS, Product::SAP_VERSIONS))
                                    ->columns(2),
                                CheckboxList::make('db_support')
                                    ->label('Cơ sở dữ liệu')
                                    ->options(Product::DB_SUPPORT)
                                    ->columns(2),
                                Textarea::make('summary')
                                    ->label('Tóm tắt')
                                    ->maxLength(300)
                                    ->rows(2)
                                    ->helperText('Hiển thị trên thẻ danh sách và meta description.')
                                    ->columnSpanFull(),
                                Repeater::make('features')
                                    ->label('Tính năng')
                                    ->simple(TextInput::make('feature')->required()->maxLength(255))
                                    ->defaultItems(0)
                                    ->addActionLabel('Thêm tính năng')
                                    ->columnSpanFull(),
                                Repeater::make('data_flow')
                                    ->label('Luồng dữ liệu')
                                    ->schema([
                                        TextInput::make('from')->label('Từ')->required()->maxLength(150),
                                        TextInput::make('to')->label('Đến')->required()->maxLength(150),
                                        TextInput::make('note')->label('Ghi chú')->maxLength(255),
                                    ])
                                    ->columns(3)
                                    ->defaultItems(0)
                                    ->addActionLabel('Thêm bước')
                                    ->columnSpanFull(),
                                Repeater::make('faqs')
                                    ->label('Câu hỏi thường gặp')
                                    ->schema([
                                        TextInput::make('q')->label('Câu hỏi')->required()->maxLength(255),
                                        Textarea::make('a')->label('Trả lời')->required()->rows(2)->maxLength(1000),
                                    ])
                                    ->defaultItems(0)
                                    ->addActionLabel('Thêm câu hỏi')
                                    ->columnSpanFull(),
                                FileUpload::make('gallery')
                                    ->label('Thư viện ảnh')
                                    ->image()
                                    ->multiple()
                                    ->reorderable()
                                    ->columnSpanFull(),
                                TextInput::make('docs_url')
                                    ->label('Link tài liệu')
                                    ->url()
                                    ->maxLength(255),
                                TextInput::make('demo_url')
                                    ->label('Link demo')
                                    ->url()
                                    ->maxLength(255),
                            ])
                            ->columns(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('integration')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('billing')
                    ->formatStateUsing(fn (?string $state): ?string => Product::BILLING_OPTIONS[$state] ?? $state)
                    ->toggleable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('sale_price')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                ImageColumn::make('image'),
                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('category_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        Product::TYPE_PHYSICAL => 'Hàng hóa',
                        Product::TYPE_ADDON => 'Addon',
                    ]),
                SelectFilter::make('integration')
                    ->options(Product::INTEGRATIONS),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}
