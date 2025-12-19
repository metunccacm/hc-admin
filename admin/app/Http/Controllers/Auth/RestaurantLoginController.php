<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class RestaurantLoginController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Show the restaurant login form.
     */
    public function showLoginForm()
    {
        if (Session::has('restaurant_id')) {
            return redirect()->route('restaurant.dashboard');
        }
        return view('auth.restaurant-login');
    }

    /**
     * Handle restaurant login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        // Get restaurant by username from database
        $restaurant = $this->supabase->getRestaurantByUsername($username);

        if ($restaurant && isset($restaurant['password']) && $restaurant['password'] === $password) {
            Session::put('restaurant_id', $restaurant['id']);
            Session::put('restaurant_name', $restaurant['name']);
            Session::put('is_restaurant_logged_in', true);

            return redirect()->route('restaurant.dashboard')
                ->with('success', 'Hoş geldiniz, ' . $restaurant['name'] . '!');
        }

        return back()->withErrors([
            'username' => 'Geçersiz kullanıcı adı veya şifre.',
        ])->withInput($request->only('username'));
    }

    /**
     * Log out the restaurant.
     */
    public function logout(Request $request)
    {
        Session::forget(['restaurant_id', 'restaurant_name', 'is_restaurant_logged_in']);
        
        return redirect()->route('restaurant.login')
            ->with('success', 'Başarıyla çıkış yaptınız.');
    }
}
