<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeChargeSucceeded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChargeSucceededEmail
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
    public function handle(StripeChargeSucceeded $event): void
    {
        //
    }
}
