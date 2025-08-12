<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
            $request->session()->put('url.intended', $request->redirect);
        }

        return view('login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // التحقق من وجود معامل redirect محفوظ في الجلسة
        $intendedUrl = $request->session()->get('url.intended');
        if ($intendedUrl) {
            $request->session()->forget('url.intended');
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
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Check if the request is from Inertia.js
        if ($request->header('X-Inertia')) {
            // For Inertia requests, use external redirect to force full page reload
            return Inertia::location(route('home'));
        }

        return redirect(route('home', absolute: false));
    }
}
