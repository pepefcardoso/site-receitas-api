<?php

namespace App\Services\NewsletterCustomer;

use App\Models\NewsletterCustomer;
use Illuminate\Support\Facades\DB;

class UpdateNewsletterCustomer
{
    public function update(NewsletterCustomer $newsletterCustomer, array $data): NewsletterCustomer|string
    {
        try {
            DB::beginTransaction();

            $newsletterCustomer->fill($data);
            $newsletterCustomer->save();

            DB::commit();

            return $newsletterCustomer;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
