<?php

namespace App\Services\NewsletterCustomer;

use App\Models\NewsletterCustomer;

class ListNewsletterCustomer
{
    public function list(int $perPage = 10)
    {
        $query = NewsletterCustomer::query();

        return $query->paginate($perPage);
    }
}
