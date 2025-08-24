<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request)
    {
        // حفظ معامل redirect في الجلسة إذا كان موجوداً
        if ($request->has('redirect')) {
            Session::put('url.intended', $request->redirect);
        }

        // Check if request expects JSON (Inertia)
        if ($request->header('X-Inertia')) {
            return Inertia::render('Auth/Login');
        }

        return view('login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        Session::regenerate();

        // Log for debugging
        Log::info('Login attempt', [
            'email' => $request->email,
            'user_role' => Auth::user()->role,
            'user_id' => Auth::user()->id,
            'session_id' => Session::getId(),
            'request_url' => $request->fullUrl(),
            'user_agent' => $request->header('User-Agent')
        ]);

        // التحقق من وجود معامل redirect محفوظ في الجلسة
        $intendedUrl = Session::get('url.intended');
        if ($intendedUrl) {
            Session::forget('url.intended');
            \Log::info('Redirecting to intended URL', ['url' => $intendedUrl]);
            return redirect($intendedUrl);
        }

        // التحقق من وجود admin redirect من الـ event listener
        $adminRedirect = Session::get('admin_redirect');
        if ($adminRedirect) {
            Session::forget('admin_redirect');
            \Log::info('Redirecting to admin redirect', ['url' => $adminRedirect]);
            return redirect($adminRedirect);
        }

        // توجيه المستخدمين برول admin إلى الـ dashboard
        if (Auth::user()->role === 'admin') {
            $dashboardUrl = route('dashboard');
            \Log::info('Admin user - redirecting to dashboard', [
                'user_role' => Auth::user()->role,
                'dashboard_url' => $dashboardUrl
            ]);
            return redirect($dashboardUrl);
        }

        // توجيه المستخدم إلى الصفحة الرئيسية إذا لم يكن هناك redirect محفوظ
        $homeUrl = route('home');
        \Log::info('Regular user - redirecting to home', [
            'user_role' => Auth::user()->role,
            'home_url' => $homeUrl
        ]);
        return redirect($homeUrl);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Logout from all guards
        Auth::guard('web')->logout();
        Auth::guard('sanctum')->logout();

        // Clear all session data
        Session::flush();
        Session::invalidate();
        Session::regenerateToken();

        // Clear all cookies
        $request->session()->forget('auth');
        $request->session()->forget('user');
        $request->session()->forget('_token');

        // Check if the request is from Inertia.js
        if ($request->header('X-Inertia')) {
            // For Inertia requests, force full page reload to login
            return Inertia::location(route('login'));
        }

        // Force redirect to login with cache busting
        return redirect(route('login'))->withHeaders([
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}
