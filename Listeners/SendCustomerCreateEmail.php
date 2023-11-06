<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeCustomerCreate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCustomerCreateEmail
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
    public function handle(StripeCustomerCreate $event): void
    {
        //
    }
}
