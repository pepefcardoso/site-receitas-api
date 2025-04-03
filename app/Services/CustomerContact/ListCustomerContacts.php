<?php

namespace App\Services\CustomerContact;

use App\Models\CustomerContact;

class ListCustomerContacts
{
    public function list(array $data)
    {
        $query = CustomerContact::query();

        return $query->paginate($data['perPage'] ?? 10);
    }
}
