<?php

namespace App\Services\NewsletterCustomer;

use App\Models\NewsletterCustomer;

class ShowNewsletterCustomer
{
    public function show($id)
    {
        return NewsletterCustomer::findOrFail($id);
    }
}
