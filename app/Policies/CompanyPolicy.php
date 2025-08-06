<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isInternal();
    }

    public function view(User $user, Company $company): bool
    {
        return $user->isInternal() || $user->id === $company->user_id;
    }

    public function create(User $user): bool
    {
        return $user->company()->doesntExist();
    }

    public function update(User $user, Company $company): bool
    {
        return $user->isInternal() || $user->id === $company->user_id;
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->isInternal() || $user->id === $company->user_id;
    }
}
