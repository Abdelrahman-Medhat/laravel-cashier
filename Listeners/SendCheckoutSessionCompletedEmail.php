<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeCheckoutSessionCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendCheckoutSessionCompletedEmail
{
    /**
     * Handle the event.
     */
    public function handle(StripeCheckoutSessionCompleted $event): void
    {
        Log::info('StripeCheckoutSessionCompleted: ' . print_r($event, true));
    }
}
