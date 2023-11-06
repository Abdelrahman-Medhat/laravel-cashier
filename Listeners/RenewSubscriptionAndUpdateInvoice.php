<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeCheckoutSessionCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use AbdelrahmanMedhat\Cashier\Cashier;
use App\Models\Invoice;
use LucasDotVin\Soulbscription\Models\Plan;

class RenewSubscriptionAndUpdateInvoice
{
    /**
     * Handle the event.
     */
    public function handle(StripeCheckoutSessionCompleted $event): void
    {
        $user = Cashier::findBillable($event->payload['data']['object']['customer']);
        $plan = Plan::find($event->payload['data']['object']['metadata']['plan_id']);

        $user->subscribeTo($plan);
    }
}
