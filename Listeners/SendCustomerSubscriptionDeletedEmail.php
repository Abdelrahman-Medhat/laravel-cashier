<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeCustomerSubscriptionDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCustomerSubscriptionDeletedEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StripeCustomerSubscriptionDeleted $event): void
    {
        //
    }
}
