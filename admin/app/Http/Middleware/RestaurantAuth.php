<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RestaurantAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('is_restaurant_logged_in') || !Session::get('is_restaurant_logged_in')) {
            return redirect()->route('restaurant.login')
                ->withErrors(['error' => 'Lütfen giriş yapın.']);
        }

        return $next($request);
    }
}
