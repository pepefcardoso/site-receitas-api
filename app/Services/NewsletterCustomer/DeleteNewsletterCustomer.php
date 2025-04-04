<?php

namespace App\Services\NewsletterCustomer;

use App\Models\NewsletterCustomer;
use App\Notifications\DeleteNewsletterCustomerNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class DeleteNewsletterCustomer
{
    public function delete(int $newsletterCustomer): NewsletterCustomer|string
    {
        try {
            DB::beginTransaction();

            $newsletterCustomer = NewsletterCustomer::findOrFail($newsletterCustomer);
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
