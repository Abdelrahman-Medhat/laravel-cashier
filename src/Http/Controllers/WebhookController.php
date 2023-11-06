<?php

namespace AbdelrahmanMedhat\Cashier\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use AbdelrahmanMedhat\Cashier\Cashier;
use AbdelrahmanMedhat\Cashier\Events\WebhookHandled;
use AbdelrahmanMedhat\Cashier\Events\WebhookReceived;
use AbdelrahmanMedhat\Cashier\Events\StripeChargeSucceeded;
use AbdelrahmanMedhat\Cashier\Events\StripeChargeFailed;
use AbdelrahmanMedhat\Cashier\Events\StripeInvoicePaymentSucceeded;
use AbdelrahmanMedhat\Cashier\Events\StripeInvoicePaymentFailed;
use AbdelrahmanMedhat\Cashier\Events\StripeCustomerSubscriptionCreated;
use AbdelrahmanMedhat\Cashier\Events\StripeCustomerSubscriptionUpdated;
use AbdelrahmanMedhat\Cashier\Events\StripeCustomerSubscriptionDeleted;
use AbdelrahmanMedhat\Cashier\Events\StripeCustomerCreate;
use AbdelrahmanMedhat\Cashier\Events\StripeCustomerUpdate;
use AbdelrahmanMedhat\Cashier\Events\StripeCustomerDelete;
use AbdelrahmanMedhat\Cashier\Events\StripePaymentIntentSucceeded;
use AbdelrahmanMedhat\Cashier\Events\StripePaymentIntentFailed;
use AbdelrahmanMedhat\Cashier\Events\StripePaymentMethodAttached;
use AbdelrahmanMedhat\Cashier\Events\StripeCheckoutSessionCompleted;
use AbdelrahmanMedhat\Cashier\Events\StripeRefundIssued;
use AbdelrahmanMedhat\Cashier\Http\Middleware\VerifyWebhookSignature;
use AbdelrahmanMedhat\Cashier\Payment;
use AbdelrahmanMedhat\Cashier\Subscription;
use Stripe\Stripe;
use Stripe\Subscription as StripeSubscription;
use Symfony\Component\HttpFoundation\Response;

class WebhookController extends Controller
{
    /**
     * Create a new WebhookController instance.
     *
     * @return void
     */
    public function __construct()
    {
        if (config('cashier.webhook.secret')) {
            $this->middleware(VerifyWebhookSignature::class);
        }
    }

    /**
     * Handle a Stripe webhook call.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleWebhook(Request $request)
    {
        $payload = json_decode($request->getContent(), true);
        $method = 'handle'.Str::studly(str_replace('.', '_', $payload['type']));

        WebhookReceived::dispatch($payload);

        $eventType = $payload['type'] ?? null;

        switch ($eventType) {
            case 'charge.succeeded':
                event(new StripeChargeSucceeded($payload));
                break;

            case 'charge.failed':
                event(new StripeChargeFailed($payload));
                break;

            case 'invoice.payment_succeeded':
                event(new StripeInvoicePaymentSucceeded($payload));
                break;

            case 'invoice.payment_failed':
                event(new StripeInvoicePaymentFailed($payload));
                break;

            case 'customer.subscription.created':
                event(new StripeCustomerSubscriptionCreated($payload));
                break;

            case 'customer.subscription.updated':
                event(new StripeCustomerSubscriptionUpdated($payload));
                break;

            case 'customer.subscription.deleted':
                event(new StripeCustomerSubscriptionDeleted($payload));
                break;

            case 'customer.created':
                event(new StripeCustomerCreate($payload));
                break;

            case 'customer.updated':
                event(new StripeCustomerUpdate($payload));
                break;

            case 'customer.deleted':
                event(new StripeCustomerDelete($payload));
                break;

            case 'payment_intent.succeeded':
                event(new StripePaymentIntentSucceeded($payload));
                break;

            case 'payment_intent.payment_failed':
                event(new StripePaymentIntentFailed($payload));
                break;

            case 'payment_method.attached':
                event(new StripePaymentMethodAttached($payload));
                break;

            case 'checkout.session.completed':
                event(new StripeCheckoutSessionCompleted($payload));
                break;

            case 'refund.issued':
                event(new StripeRefundIssued($payload));
                break;

            default:
                break;
        }

        if (method_exists($this, $method)) {
            $this->setMaxNetworkRetries();

            $response = $this->{$method}($payload);

            WebhookHandled::dispatch($payload);

            return $response;
        }

        return $this->missingMethod($payload);
    }

    /**
     * Handle customer subscription created.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionCreated(array $payload)
    {
        $user = $this->getUserByStripeId($payload['data']['object']['customer']);

        if ($user) {
            $data = $payload['data']['object'];

            if (! $user->subscriptions->contains('stripe_id', $data['id'])) {
                if (isset($data['trial_end'])) {
                    $trialEndsAt = Carbon::createFromTimestamp($data['trial_end']);
                } else {
                    $trialEndsAt = null;
                }

                $firstItem = $data['items']['data'][0];
                $isSinglePrice = count($data['items']['data']) === 1;

                $subscription = $user->subscriptions()->create([
                    'name' => $data['metadata']['name'] ?? $this->newSubscriptionName($payload),
                    'stripe_id' => $data['id'],
                    'stripe_status' => $data['status'],
                    'stripe_price' => $isSinglePrice ? $firstItem['price']['id'] : null,
                    'quantity' => $isSinglePrice && isset($firstItem['quantity']) ? $firstItem['quantity'] : null,
                    'trial_ends_at' => $trialEndsAt,
                    'ends_at' => null,
                ]);

                foreach ($data['items']['data'] as $item) {
                    $subscription->items()->create([
                        'stripe_id' => $item['id'],
                        'stripe_product' => $item['price']['product'],
                        'stripe_price' => $item['price']['id'],
                        'quantity' => $item['quantity'] ?? null,
                    ]);
                }
            }
        }

        return $this->successMethod();
    }

    /**
     * Determines the name that should be used when new subscriptions are created from the Stripe dashboard.
     *
     * @param  array  $payload
     * @return string
     */
    protected function newSubscriptionName(array $payload)
    {
        return 'default';
    }

    /**
     * Handle customer subscription updated.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionUpdated(array $payload)
    {
        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            $data = $payload['data']['object'];

            $subscription = $user->subscriptions()->firstOrNew(['stripe_id' => $data['id']]);

            if (
                isset($data['status']) &&
                $data['status'] === StripeSubscription::STATUS_INCOMPLETE_EXPIRED
            ) {
                $subscription->items()->delete();
                $subscription->delete();

                return;
            }

            $subscription->name = $subscription->name ?? $data['metadata']['name'] ?? $this->newSubscriptionName($payload);

            $firstItem = $data['items']['data'][0];
            $isSinglePrice = count($data['items']['data']) === 1;

            // Price...
            $subscription->stripe_price = $isSinglePrice ? $firstItem['price']['id'] : null;

            // Quantity...
            $subscription->quantity = $isSinglePrice && isset($firstItem['quantity']) ? $firstItem['quantity'] : null;

            // Trial ending date...
            if (isset($data['trial_end'])) {
                $trialEnd = Carbon::createFromTimestamp($data['trial_end']);

                if (! $subscription->trial_ends_at || $subscription->trial_ends_at->ne($trialEnd)) {
                    $subscription->trial_ends_at = $trialEnd;
                }
            }

            // Cancellation date...
            if (isset($data['cancel_at_period_end'])) {
                if ($data['cancel_at_period_end']) {
                    $subscription->ends_at = $subscription->onTrial()
                        ? $subscription->trial_ends_at
                        : Carbon::createFromTimestamp($data['current_period_end']);
                } elseif (isset($data['cancel_at'])) {
                    $subscription->ends_at = Carbon::createFromTimestamp($data['cancel_at']);
                } else {
                    $subscription->ends_at = null;
                }
            }

            // Status...
            if (isset($data['status'])) {
                $subscription->stripe_status = $data['status'];
            }

            $subscription->save();

            // Update subscription items...
            if (isset($data['items'])) {
                $subscriptionItemIds = [];

                foreach ($data['items']['data'] as $item) {
                    $subscriptionItemIds[] = $item['id'];

                    $subscription->items()->updateOrCreate([
                        'stripe_id' => $item['id'],
                    ], [
                        'stripe_product' => $item['price']['product'],
                        'stripe_price' => $item['price']['id'],
                        'quantity' => $item['quantity'] ?? null,
                    ]);
                }

                // Delete items that aren't attached to the subscription anymore...
                $subscription->items()->whereNotIn('stripe_id', $subscriptionItemIds)->delete();
            }
        }

        return $this->successMethod();
    }

    /**
     * Handle a canceled customer from a Stripe subscription.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerSubscriptionDeleted(array $payload)
    {
        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            $user->subscriptions->filter(function ($subscription) use ($payload) {
                return $subscription->stripe_id === $payload['data']['object']['id'];
            })->each(function ($subscription) {
                $subscription->markAsCanceled();
            });
        }

        return $this->successMethod();
    }

    /**
     * Handle customer updated.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerUpdated(array $payload)
    {
        if ($user = $this->getUserByStripeId($payload['data']['object']['id'])) {
            $user->updateDefaultPaymentMethodFromStripe();
        }

        return $this->successMethod();
    }

    /**
     * Handle deleted customer.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleCustomerDeleted(array $payload)
    {
        if ($user = $this->getUserByStripeId($payload['data']['object']['id'])) {
            $user->subscriptions->each(function (Subscription $subscription) {
                $subscription->skipTrial()->markAsCanceled();
            });

            $user->forceFill([
                'stripe_id' => null,
                'trial_ends_at' => null,
                'pm_type' => null,
                'pm_last_four' => null,
            ])->save();
        }

        return $this->successMethod();
    }

    /**
     * Handle payment method automatically updated by vendor.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handlePaymentMethodAutomaticallyUpdated(array $payload)
    {
        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            $user->updateDefaultPaymentMethodFromStripe();
        }

        return $this->successMethod();
    }

    /**
     * Handle payment action required for invoice.
     *
     * @param  array  $payload
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleInvoicePaymentActionRequired(array $payload)
    {
        if (is_null($notification = config('cashier.payment_notification'))) {
            return $this->successMethod();
        }

        if ($user = $this->getUserByStripeId($payload['data']['object']['customer'])) {
            if (in_array(Notifiable::class, class_uses_recursive($user))) {
                $payment = new Payment($user->stripe()->paymentIntents->retrieve(
                    $payload['data']['object']['payment_intent']
                ));

                $user->notify(new $notification($payment));
            }
        }

        return $this->successMethod();
    }

    /**
     * Get the customer instance by Stripe ID.
     *
     * @param  string|null  $stripeId
     * @return \AbdelrahmanMedhat\Cashier\Billable|null
     */
    protected function getUserByStripeId($stripeId)
    {
        return Cashier::findBillable($stripeId);
    }

    /**
     * Handle successful calls on the controller.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function successMethod($parameters = [])
    {
        return new Response('Webhook Handled', 200);
    }

    /**
     * Handle calls to missing methods on the controller.
     *
     * @param  array  $parameters
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function missingMethod($parameters = [])
    {
        return new Response;
    }

    /**
     * Set the number of automatic retries due to an object lock timeout from Stripe.
     *
     * @param  int  $retries
     * @return void
     */
    protected function setMaxNetworkRetries($retries = 3)
    {
        Stripe::setMaxNetworkRetries($retries);
    }
}
