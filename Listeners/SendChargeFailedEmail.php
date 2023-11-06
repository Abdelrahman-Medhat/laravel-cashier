<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeChargeFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChargeFailedEmail
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
    public function handle(StripeChargeFailed $event): void
    {
        //
    }
}
