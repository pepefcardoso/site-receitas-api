<?php

namespace App\Services\NewsletterCustomer;

use App\Models\NewsletterCustomer;
use App\Notifications\DeleteNewsletterCustomerNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class DeleteNewsletterCustomer
{
    public function delete(NewsletterCustomer $newsletterCustomer): NewsletterCustomer|string
    {
        try {
            DB::beginTransaction();

            $newsletterCustomer->delete();

            Notification::route('mail', $newsletterCustomer->email)
                ->notify(new DeleteNewsletterCustomerNotification($newsletterCustomer));

            DB::commit();

            return $newsletterCustomer;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
