<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeCustomerUpdate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCustomerUpdateEmail
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
    public function handle(StripeCustomerUpdate $event): void
    {
        //
    }
}
