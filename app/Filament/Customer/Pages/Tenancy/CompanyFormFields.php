<?php

namespace App\Filament\Customer\Pages\Tenancy;

use App\Models\Company;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\Rules\Unique;

/** Fields shared by the company registration and edit pages. */
class CompanyFormFields
{
    /**
     * @return array<int, TextInput>
     */
    public static function components(): array
    {
        return [
            TextInput::make('name')
                ->label('Tên công ty')
                ->required()
                ->maxLength(255),
            TextInput::make('mst')
                ->label('Mã số thuế')
                ->helperText('10 số, hoặc 13 ký tự cho chi nhánh (vd: 0123456789-001).')
                ->required()
                ->regex('/^\d{10}(-\d{3})?$/')
                ->validationMessages(['regex' => 'Mã số thuế gồm 10 số hoặc 10 số kèm -XXX.'])
                ->unique(
                    table: Company::class,
                    column: 'mst',
                    ignoreRecord: true,
                    modifyRuleUsing: fn (Unique $rule) => $rule->where('owner_id', auth('customer')->id()),
                ),
            TextInput::make('address')
                ->label('Địa chỉ')
                ->maxLength(255),
        ];
    }
}
