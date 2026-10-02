<?php

namespace App\Policies;

use App\Models\TaxLicense;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Licenses are administered by site admins (the `web` guard `User`) only. Any other authenticatable,
 * customers above all, is denied; existing admin access rules are unchanged.
 */
class TaxLicensePolicy
{
    public function viewAny(Authenticatable $user): bool
    {
        return $user instanceof User;
    }

    public function view(Authenticatable $user, TaxLicense $license): bool
    {
        return $user instanceof User;
    }

    public function create(Authenticatable $user): bool
    {
        return $user instanceof User;
    }

    public function update(Authenticatable $user, TaxLicense $license): bool
    {
        return $user instanceof User;
    }

    public function delete(Authenticatable $user, TaxLicense $license): bool
    {
        return $user instanceof User;
    }
}
