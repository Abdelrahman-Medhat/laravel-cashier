<?php

namespace AbdelrahmanMedhat\Cashier\Listeners;

use AbdelrahmanMedhat\Cashier\Events\WebhookReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

use App\Events\StripeChargeSucceeded;
use App\Events\StripeChargeFailed;
use App\Events\StripeInvoicePaymentSucceeded;
use App\Events\StripeInvoicePaymentFailed;
use App\Events\StripeCustomerSubscriptionCreated;
use App\Events\StripeCustomerSubscriptionUpdated;
use App\Events\StripeCustomerSubscriptionDeleted;
use App\Events\StripeCustomerCreate;
use App\Events\StripeCustomerUpdate;
use App\Events\StripeCustomerDelete;
use App\Events\StripePaymentIntentSucceeded;
use App\Events\StripePaymentIntentFailed;
use App\Events\StripePaymentMethodAttached;
use App\Events\StripeCheckoutSessionCompleted;
use App\Events\StripeRefundIssued;


class StripeEventListener
{
    /**
     * Handle received Stripe webhooks.
     */
    public function handle(WebhookReceived $event): void
    {
        $eventType = $event->payload['type'] ?? null;

        switch ($eventType) {
            case 'charge.succeeded':
                event(new StripeChargeSucceeded($event->payload));
                break;

            case 'charge.failed':
                event(new StripeChargeFailed($event->payload));
                break;

            case 'invoice.payment_succeeded':
                event(new StripeInvoicePaymentSucceeded($event->payload));
                break;

            case 'invoice.payment_failed':
                event(new StripeInvoicePaymentFailed($event->payload));
                break;

            case 'customer.subscription.created':
                event(new StripeCustomerSubscriptionCreated($event->payload));
                break;

            case 'customer.subscription.updated':
                event(new StripeCustomerSubscriptionUpdated($event->payload));
                break;

            case 'customer.subscription.deleted':
                event(new StripeCustomerSubscriptionDeleted($event->payload));
                break;

            case 'customer.created':
                event(new StripeCustomerCreate($event->payload));
                break;

            case 'customer.updated':
                event(new StripeCustomerUpdate($event->payload));
                break;

            case 'customer.deleted':
                event(new StripeCustomerDelete($event->payload));
                break;

            case 'payment_intent.succeeded':
                event(new StripePaymentIntentSucceeded($event->payload));
                break;

            case 'payment_intent.payment_failed':
                event(new StripePaymentIntentFailed($event->payload));
                break;

            case 'payment_method.attached':
                event(new StripePaymentMethodAttached($event->payload));
                break;

            case 'checkout.session.completed':
                event(new StripeCheckoutSessionCompleted($event->payload));
                break;

            case 'refund.issued':
                event(new StripeRefundIssued($event->payload));
                break;

            default:
                break;
        }
    }
}
