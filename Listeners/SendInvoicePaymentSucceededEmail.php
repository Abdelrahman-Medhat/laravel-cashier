<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use App\Events\StripeInvoicePaymentSucceeded;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use AbdelrahmanMedhat\Cashier\Cashier;
use App\Models\Invoice;
use LucasDotVin\Soulbscription\Models\Plan;

class SendInvoicePaymentSucceededEmail
{
    /**
     * Handle the event.
     */
    public function handle(StripeInvoicePaymentSucceeded $event): void
    {
        $user = Cashier::findBillable($event->payload['data']['object']['customer']);
        $invoice = $user->invoices()->create([
            'amount' => $event->payload['data']['object']['amount_paid'],
            'stripe_invoice_id' => $event->payload['data']['object']['id'],
            'stripe_invoice_pdf' => $event->payload['data']['object']['invoice_pdf'],
        ]);

    }
}
