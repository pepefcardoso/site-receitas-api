<?php

namespace App\Policies;

use App\Models\CustomerContact;
use App\Models\User;

class CustomerContactPolicy
{
    /**
     * Determine se o usuário pode ver a lista de contatos.
     */
    public function viewAny(User $user): bool
    {
        return $user->isInternal();
    }

    /**
     * Determine se o usuário pode ver um contato específico.
     */
    public function view(User $user, CustomerContact $customerContact): bool
    {
        return $user->isInternal();
    }

    /**
     * Determine se o usuário pode atualizar um contato.
     */
    public function update(User $user, CustomerContact $customerContact): bool
    {
        return $user->isInternal();
    }
}
