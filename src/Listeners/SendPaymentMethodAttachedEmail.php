<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripePaymentMethodAttached;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentMethodAttachedEmail
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
    public function handle(StripePaymentMethodAttached $event): void
    {
        //
    }
}
