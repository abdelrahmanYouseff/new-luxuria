<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Booking;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class ApiCheckoutController extends Controller
{
    public function createCheckout(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date|after:today',
            'end_date' => 'required|date|after:start_date',
            'emirate' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
            'user_id' => 'nullable|integer|exists:users,id',
            'user_email' => 'nullable|email',
            'user_name' => 'nullable|string|max:255',
        ]);

        try {
            $vehicle = Vehicle::findOrFail($request->vehicle_id);

            // Resolve user: prefer authenticated token -> user_id -> email create/find
            $user = $request->user();
            if (!$user) {
                if ($request->filled('user_id')) {
                    $user = User::findOrFail($request->integer('user_id'));
                } elseif ($request->filled('user_email')) {
                    $user = User::where('email', $request->string('user_email'))->first();
                    if (!$user) {
                        $user = User::create([
                            'name' => $request->string('user_name') ?: 'Mobile User',
                            'email' => $request->string('user_email'),
                            'password' => Hash::make(Str::random(24)),
                            'role' => 'user',
                        ]);
                    }
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'User context required: provide auth token, user_id, or user_email.',
                    ], 422);
                }
            }

            // Pricing
            $start = Carbon::parse($request->start_date);
            $end = Carbon::parse($request->end_date);
            $totalDays = $start->diffInDays($end) + 1;

            $pricingType = 'daily';
            $appliedRate = (float) $vehicle->daily_rate;
            if ($totalDays > 7 && $totalDays < 28 && (float) $vehicle->weekly_rate > 0) {
                $pricingType = 'weekly';
                $appliedRate = (float) $vehicle->weekly_rate / 7;
            } elseif ($totalDays >= 28 && (float) $vehicle->monthly_rate > 0) {
                $pricingType = 'monthly';
                $appliedRate = round(((float) $vehicle->monthly_rate) / 30, 2);
            }
            if ($appliedRate <= 0) {
                $appliedRate = max((float) $vehicle->daily_rate, 1);
            }
            $totalAmount = round($totalDays * $appliedRate, 2);

            // Create pending booking locally
            $booking = Booking::create([
                'user_id' => $user->id,
                'vehicle_id' => $vehicle->id,
                'emirate' => $request->emirate,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'status' => 'pending',
                'daily_rate' => $vehicle->daily_rate,
                'pricing_type' => $pricingType,
                'applied_rate' => $appliedRate,
                'total_days' => $totalDays,
                'total_amount' => $totalAmount,
                'notes' => $request->notes,
            ]);

            // Create Stripe checkout session
            Stripe::setApiKey(config('services.stripe.secret'));
            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'aed',
                        'product_data' => [
                            'name' => 'Vehicle Rental - ' . ($vehicle->make . ' ' . $vehicle->model),
                            'description' => 'Rental period: ' . $start->format('d/m/Y') . ' to ' . $end->format('d/m/Y') . ' (' . $totalDays . ' days)',
                        ],
                        'unit_amount' => $totalAmount * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('booking.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('cars.show', ['id' => $vehicle->id]),
                'metadata' => [
                    'booking_id' => $booking->id,
                    'user_id' => $user->id,
                    'vehicle_id' => $vehicle->id,
                ],
            ]);

            Log::info('API checkout created', [
                'booking_id' => $booking->id,
                'session_id' => $session->id,
                'amount' => $totalAmount,
            ]);

            return response()->json([
                'success' => true,
                'payment_url' => $session->url,
                'session_id' => $session->id,
                'booking_id' => $booking->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('API checkout error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to create checkout session',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

