<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('is_admin_logged_in') || !Session::get('is_admin_logged_in')) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'Lütfen admin olarak giriş yapın.']);
        }

        return $next($request);
    }
}
