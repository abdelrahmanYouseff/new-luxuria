<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\CouponInvoice;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentConfirmationMail extends Mailable
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
            from: new Address(config('mail.from.address', 'noreply@luxuriauae.com'), config('mail.from.name', 'Luxuria UAE')),
            replyTo: [new Address('support@luxuriauae.com', 'Support')],
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
        // Attach invoice PDF
        try {
            $pdf = Pdf::loadView('invoices.pdf', [
                'invoice' => $this->invoice,
            ])->setPaper('a4', 'portrait');

            $output = $pdf->output();

            return [
                Attachment::fromData(fn () => $output, 'invoice_' . $this->invoice->invoice_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        } catch (\Throwable $e) {
            return [];
        }
    }
}
