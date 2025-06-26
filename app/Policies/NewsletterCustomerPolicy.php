<?php

namespace App\Policies;

use App\Models\NewsletterCustomer;
use App\Models\User;

class NewsletterCustomerPolicy
{
    /**
     * Determine se o usuário pode ver a lista de inscritos.
     */
    public function viewAny(User $user): bool
    {
        return $user->isInternal();
    }

    /**
     * Determine se o usuário pode ver um inscrito específico.
     */
    public function view(User $user, NewsletterCustomer $newsletterCustomer): bool
    {
        return $user->isInternal();
    }

    /**
     * Determine se o usuário pode remover um inscrito.
     */
    public function delete(User $user, NewsletterCustomer $newsletterCustomer): bool
    {
        return $user->isInternal();
    }
}
