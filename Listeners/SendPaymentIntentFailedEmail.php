<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripePaymentIntentFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentIntentFailedEmail
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
    public function handle(StripePaymentIntentFailed $event): void
    {
        //
    }
}
