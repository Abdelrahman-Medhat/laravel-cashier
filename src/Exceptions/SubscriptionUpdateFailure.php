<?php

namespace AbdelrahmanMedhat\Cashier\Exceptions;

use Exception;
use AbdelrahmanMedhat\Cashier\Subscription;

class SubscriptionUpdateFailure extends Exception
{
    /**
     * Create a new SubscriptionUpdateFailure instance.
     *
     * @param  \AbdelrahmanMedhat\Cashier\Subscription  $subscription
     * @return static
     */
    public static function incompleteSubscription(Subscription $subscription)
    {
        return new static(
            "The subscription \"{$subscription->cashier_stripe_id}\" cannot be updated because its payment is incomplete."
        );
    }

    /**
     * Create a new SubscriptionUpdateFailure instance.
     *
     * @param  \AbdelrahmanMedhat\Cashier\Subscription  $subscription
     * @param  string  $price
     * @return static
     */
    public static function duplicatePrice(Subscription $subscription, $price)
    {
        return new static(
            "The price \"$price\" is already attached to subscription \"{$subscription->cashier_stripe_id}\"."
        );
    }

    /**
     * Create a new SubscriptionUpdateFailure instance.
     *
     * @param  \AbdelrahmanMedhat\Cashier\Subscription  $subscription
     * @return static
     */
    public static function cannotDeleteLastPrice(Subscription $subscription)
    {
        return new static(
            "The price on subscription \"{$subscription->cashier_stripe_id}\" cannot be removed because it is the last one."
        );
    }
}
