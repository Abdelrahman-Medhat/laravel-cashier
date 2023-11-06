<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeRefundIssued;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendRefundIssuedEmail
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
    public function handle(StripeRefundIssued $event): void
    {
        //
    }
}
