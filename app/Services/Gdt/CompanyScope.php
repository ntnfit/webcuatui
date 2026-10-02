<?php

namespace App\Services\Gdt;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Narrows company-owned models to the Filament tenant that is currently active.
 * Queue jobs and console code have no tenant and must filter by company_id explicitly.
 */
class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = Filament::getTenant();

        if ($tenant !== null) {
            $builder->where($model->qualifyColumn('company_id'), $tenant->getKey());
        }
    }
}
