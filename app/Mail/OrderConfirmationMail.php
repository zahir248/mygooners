<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $invoicePath;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $invoicePath = null)
    {
        $this->order = $order;
        $this->invoicePath = $invoicePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengesahan Pesanan #' . $this->order->order_number . ' - MyGooners',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
            with: [
                'order' => $this->order,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];

        if ($this->invoicePath && file_exists($this->invoicePath)) {
            $attachments[] = Attachment::fromPath($this->invoicePath)
                ->as('Invoice_' . $this->order->order_number . '.pdf')
                ->withMime('application/pdf');
        }

        return $attachments;
    }
} 