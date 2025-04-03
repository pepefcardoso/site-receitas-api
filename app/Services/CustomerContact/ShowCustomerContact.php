<?php

namespace App\Services\CustomerContact;

use App\Models\CustomerContact;

class ShowCustomerContact
{
    public function show(int $id)
    {
        return CustomerContact::findOrFail($id);
    }
}
