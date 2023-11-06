<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeCustomerSubscriptionCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCustomerSubscriptionCreatedEmail
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
    public function handle(StripeCustomerSubscriptionCreated $event): void
    {
        //
    }
}
