<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;
use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $user;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->user = $booking->user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmed - Your Vehicle is Reserved! | Luxuria UAE',
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
            view: 'emails.booking-confirmation',
            with: [
                'booking' => $this->booking,
                'user' => $this->user,
                'vehicle' => $this->booking->vehicle,
                'customerName' => $this->user->name,
                'bookingId' => $this->booking->id,
                'vehicleName' => $this->booking->vehicle->make . ' ' . $this->booking->vehicle->model,
                'startDate' => optional($this->booking->start_date)->format('d/m/Y'),
                'endDate' => optional($this->booking->end_date)->format('d/m/Y'),
                'totalAmount' => $this->booking->total_amount,
                'currency' => 'AED',
                'pickupLocation' => $this->booking->pickup_location ?? 'Not specified',
                'dropoffLocation' => $this->booking->dropoff_location ?? 'Not specified',
                'bookingDate' => $this->booking->created_at->format('d/m/Y H:i'),
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
        // Generate a simple PDF invoice for the booking and attach it
        try {
            $pdf = Pdf::loadView('invoices.booking_pdf', [
                'booking' => $this->booking,
                'user' => $this->user,
                'vehicle' => $this->booking->vehicle,
            ])->setPaper('a4', 'portrait');

            $pdfOutput = $pdf->output();

            return [
                Attachment::fromData(fn () => $pdfOutput, 'booking_invoice_'. $this->booking->id .'.pdf')
                    ->withMime('application/pdf'),
            ];
        } catch (\Throwable $e) {
            // If PDF generation fails, send email without attachment
            return [];
        }
    }
}
