<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeCustomerSubscriptionUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCustomerSubscriptionUpdatedEmail
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
    public function handle(StripeCustomerSubscriptionUpdated $event): void
    {
        //
    }
}
