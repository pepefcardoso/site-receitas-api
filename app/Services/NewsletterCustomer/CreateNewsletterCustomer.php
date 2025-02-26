<?php

use App\Models\NewsletterCustomer;
use App\Notifications\CreateNewsletterCustomerNotification;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class CreateNewsletterCustomer
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $newsletterCustomer = NewsletterCustomer::create($data);

            Notification::route('mail', $newsletterCustomer->email)
                ->notify(new CreateNewsletterCustomerNotification($newsletterCustomer));

            DB::commit();

            return $newsletterCustomer;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
