<?php

namespace App\Services\CustomerContact;

use App\Models\CustomerContact;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomerContactNotification;

class CreateCustomerContact
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $customerContact = CustomerContact::create($data);

            Notification::route('mail', $customerContact->email)
                ->notify(new CustomerContactNotification($customerContact));

            DB::commit();

            return $customerContact;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
