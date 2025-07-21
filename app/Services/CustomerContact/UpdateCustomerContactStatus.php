<?php

namespace App\Services\CustomerContact;

use App\Models\CustomerContact;
use Illuminate\Support\Facades\DB;

class UpdateCustomerContactStatus
{
    public function update(CustomerContact $contact, int $newStatus): CustomerContact
    {

        try {
            DB::beginTransaction();

            $contact->status = $newStatus;
            $contact->save();

            DB::commit();
            return $contact;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
