<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PointSysService;
use App\Services\ExternalCustomerService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    private PointSysService $pointSysService;
    private ExternalCustomerService $externalCustomerService;

    public function __construct(PointSysService $pointSysService, ExternalCustomerService $externalCustomerService)
    {
        $this->pointSysService = $pointSysService;
        $this->externalCustomerService = $externalCustomerService;
    }

    /**
     * Show the registration page.
     */
    public function create()
    {
        return view('register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
        public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'nullable|string|max:20',
            'emirate' => 'required|string|max:100',
            'address' => 'required|string|max:500',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'emirate' => $request->emirate,
            'address' => $request->address,
            'password' => Hash::make($request->password),
        ]);

        // Register customer in External API
        $externalCustomerResult = $this->externalCustomerService->createExternalCustomer([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone ?? '050' . rand(1000000, 9999999),
        ]);

        if ($externalCustomerResult['success'] && $externalCustomerResult['external_customer_id']) {
            // Store external customer ID in user record
            $user->update([
                'external_customer_id' => $externalCustomerResult['external_customer_id']
            ]);

            Log::info('Customer registered in External API successfully', [
                'user_id' => $user->id,
                'external_customer_id' => $externalCustomerResult['external_customer_id']
            ]);
        } else {
            Log::warning('Failed to register customer in External API', [
                'user_id' => $user->id,
                'error' => $externalCustomerResult['message']
            ]);
        }

        // Register in PointSys with original data first, then search for existing customer
        $pointSysRegistered = false;
        $attempts = 0;
        $maxAttempts = 2;

        // Track start time to avoid long execution
        $registrationStartTime = microtime(true);

        while (!$pointSysRegistered && $attempts < $maxAttempts) {
            // Check if we've been running for more than 15 seconds total
            if ((microtime(true) - $registrationStartTime) > 15) {
                Log::warning('PointSys registration timeout after 15 seconds', [
                    'user_id' => $user->id,
                    'attempts' => $attempts
                ]);
                break;
            }

            try {
                $customerData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?? '050' . rand(1000000, 9999999),
                ];

                // First attempt: Use original data
                if ($attempts === 0) {
                    Log::info('Attempting PointSys registration with original data', [
                        'user_id' => $user->id,
                        'email' => $customerData['email'],
                        'phone' => $customerData['phone'],
                        'attempt' => $attempts + 1
                    ]);
                }

                // Second attempt: If email exists, try to find and link existing customer
                if ($attempts === 1) {
                    Log::info('Email exists in PointSys, searching for existing customer', [
                        'user_id' => $user->id,
                        'email' => $customerData['email']
                    ]);

                    // Search for existing customer
                    $foundCustomerId = $this->findExistingPointSysCustomer($request->email);

                    if ($foundCustomerId) {
                        // Link to existing customer
                        $user->update([
                            'pointsys_customer_id' => $foundCustomerId
                        ]);

                        Log::info('Linked to existing PointSys customer', [
                            'user_id' => $user->id,
                            'pointsys_customer_id' => $foundCustomerId,
                            'email' => $request->email
                        ]);

                        $pointSysRegistered = true;
                        break;
                    } else {
                        // Create with modified email as last resort
                        $emailParts = explode('@', $request->email);
                        $customerData['email'] = $emailParts[0] . '_' . time() . '_' . $attempts . '@' . $emailParts[1];
                        $customerData['phone'] = $request->phone ? $request->phone . '_' . time() . '_' . $attempts : '050' . rand(1000000, 9999999);

                        Log::info('Creating new customer with modified email', [
                            'user_id' => $user->id,
                            'original_email' => $request->email,
                            'modified_email' => $customerData['email'],
                            'attempt' => $attempts + 1
                        ]);
                    }
                }

                $pointSysResponse = $this->pointSysService->registerCustomer($customerData);

                if ($pointSysResponse && isset($pointSysResponse['data']['customer_id'])) {
                    // Always update pointsys_customer_id when PointSys registration succeeds
                    $user->update([
                        'pointsys_customer_id' => $pointSysResponse['data']['customer_id']
                    ]);

                    Log::info('Customer registered in PointSys successfully', [
                        'user_id' => $user->id,
                        'pointsys_customer_id' => $pointSysResponse['data']['customer_id'],
                        'attempt' => $attempts + 1,
                        'email_used' => $customerData['email'],
                        'is_original_data' => $attempts === 0
                    ]);

                    $pointSysRegistered = true;
                } else {
                    // Check if it's an email already exists error
                    if ($pointSysResponse && isset($pointSysResponse['errors']['email'])) {
                        Log::warning('Email already exists in PointSys', [
                            'user_id' => $user->id,
                            'email' => $customerData['email'],
                            'attempt' => $attempts + 1
                        ]);
                    } else {
                        Log::warning('Failed to register customer in PointSys', [
                            'user_id' => $user->id,
                            'response' => $pointSysResponse,
                            'attempt' => $attempts + 1,
                            'data_sent' => $customerData
                        ]);
                    }
                    $attempts++;
                }
            } catch (\Exception $e) {
                Log::error('Exception while registering customer in PointSys', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'attempt' => $attempts + 1
                ]);
                $attempts++;
            }
        }

        if (!$pointSysRegistered) {
            Log::warning('Failed to register customer in PointSys after all attempts', [
                'user_id' => $user->id,
                'attempts' => $attempts
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return to_route('home');
    }

    /**
     * Find existing PointSys customer by email
     *
     * @param string $email
     * @return int|null
     */
    private function findExistingPointSysCustomer(string $email): ?int
    {
        try {
            // Search through customer IDs to find one with matching email
            // Limited search to avoid timeout - only check first 50 customers
            $maxCustomerId = 50; // Reduced from 200 to 50
            $startTime = microtime(true);

            for ($i = 1; $i <= $maxCustomerId; $i++) {
                // Check if we've been running for more than 10 seconds
                if ((microtime(true) - $startTime) > 10) {
                    Log::warning('PointSys customer search timeout after 10 seconds', [
                        'email' => $email,
                        'customers_checked' => $i
                    ]);
                    break;
                }

                try {
                    $balance = $this->pointSysService->getCustomerBalance($i);

                    if ($balance && isset($balance['data']['email']) && $balance['data']['email'] === $email) {
                        return $i;
                    }

                    // Very small delay to avoid rate limiting
                    usleep(10000); // Reduced from 50ms to 10ms

                } catch (\Exception $e) {
                    // Continue searching even if one request fails
                    continue;
                }
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error while searching for existing PointSys customer', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
