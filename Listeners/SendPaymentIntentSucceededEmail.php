<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripePaymentIntentSucceeded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentIntentSucceededEmail
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
    public function handle(StripePaymentIntentSucceeded $event): void
    {
        //
    }
}
