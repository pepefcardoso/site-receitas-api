<?php

namespace App\Services\CustomerContact;

use App\Models\CustomerContact;
use Illuminate\Support\Facades\DB;

class UpdateCustomerContactStatus
{
    public function update(int $contactId, int $newStatus): CustomerContact
    {

        try {
            DB::beginTransaction();

            $contact = CustomerContact::findOrFail($contactId);
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
