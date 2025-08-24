<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
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

        return view('login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        Session::regenerate();

        // التحقق من وجود معامل redirect محفوظ في الجلسة
        $intendedUrl = Session::get('url.intended');
        if ($intendedUrl) {
            Session::forget('url.intended');
            return redirect($intendedUrl);
        }

        // توجيه المستخدم إلى الصفحة الرئيسية إذا لم يكن هناك redirect محفوظ
        return redirect()->route('home');
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
