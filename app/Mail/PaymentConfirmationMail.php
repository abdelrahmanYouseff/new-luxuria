<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\CouponInvoice;

class PaymentConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invoice;
    public $couponDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(CouponInvoice $invoice, array $couponDetails = [])
    {
        $this->invoice = $invoice;
        $this->couponDetails = $couponDetails;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Successful - Your Coupon is Ready! | Luxuria UAE',
            from: config('mail.from.address', 'noreply@luxuriauae.com'),
            replyTo: 'support@luxuriauae.com',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-confirmation',
            with: [
                'invoice' => $this->invoice,
                'couponDetails' => $this->couponDetails,
                'customerName' => $this->invoice->customer_name,
                'invoiceNumber' => $this->invoice->invoice_number,
                'amount' => $this->invoice->amount,
                'currency' => $this->invoice->currency,
                'couponName' => $this->invoice->coupon_name,
                'paidAt' => $this->invoice->paid_at,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
