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

        // Also register in PointSys as backup (existing functionality)
        $pointSysRegistered = false;
        $attempts = 0;
        $maxAttempts = 3;

        while (!$pointSysRegistered && $attempts < $maxAttempts) {
            try {
                $customerData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone ?? '050' . rand(1000000, 9999999),
                ];

                // If phone is provided and this is a retry, generate a unique phone number
                if ($request->phone && $attempts > 0) {
                    $customerData['phone'] = $request->phone . '_' . time() . '_' . $attempts;
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
                        'attempt' => $attempts + 1
                    ]);

                    $pointSysRegistered = true;
                } else {
                    Log::warning('Failed to register customer in PointSys', [
                        'user_id' => $user->id,
                        'response' => $pointSysResponse,
                        'attempt' => $attempts + 1
                    ]);
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
}
