<?php

namespace AbdelrahmanMedhat\Cashier\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use AbdelrahmanMedhat\Cashier\Payment;

class ConfirmPayment extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The PaymentIntent identifier.
     *
     * @var string
     */
    public $paymentId;

    /**
     * The payment amount.
     *
     * @var string
     */
    public $amount;

    /**
     * Create a new payment confirmation notification.
     *
     * @param  \AbdelrahmanMedhat\Cashier\Payment  $payment
     * @return void
     */
    public function __construct(Payment $payment)
    {
        $this->paymentId = $payment->id;
        $this->amount = $payment->amount();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = route('cashier.payment', ['id' => $this->paymentId]);

        return (new MailMessage)
            ->subject(__('Confirm Payment'))
            ->greeting(__('Confirm your :amount payment', ['amount' => $this->amount]))
            ->line(__('Extra confirmation is needed to process your payment. Please continue to the payment page by clicking on the button below.'))
            ->action(__('Confirm Payment'), $url);
    }
}
