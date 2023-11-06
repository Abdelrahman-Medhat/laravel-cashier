<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeInvoicePaymentFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendInvoicePaymentFailedEmail
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
    public function handle(StripeInvoicePaymentFailed $event): void
    {
        //
    }
}
