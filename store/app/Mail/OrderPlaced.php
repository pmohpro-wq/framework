<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address as MailAddress;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Vanilo\Order\Contracts\Order;

class OrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        $from = config('mail.from');

        return new Envelope(
            from: new MailAddress($from['address'], $from['name'] ?? 'فروشگاه من'),
            subject: 'تأیید سفارش ' . $this->order->number . ' — فروشگاه من',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order_placed',
            with: ['order' => $this->order],
        );
    }
}
