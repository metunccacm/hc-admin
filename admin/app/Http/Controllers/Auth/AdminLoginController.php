<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AdminLoginController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        if (Session::has('is_admin_logged_in') && Session::get('is_admin_logged_in')) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.admin-login');
    }

    /**
     * Handle admin login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $adminUsername = config('services.admin.username');
        $adminPassword = config('services.admin.password');

        if ($request->input('username') === $adminUsername && $request->input('password') === $adminPassword) {
            Session::put('is_admin_logged_in', true);
            Session::put('admin_username', $adminUsername);

            return redirect()->route('admin.dashboard')
                ->with('success', 'Hoş geldiniz, Admin!');
        }

        return back()->withErrors([
            'username' => 'Geçersiz kullanıcı adı veya şifre.',
        ])->withInput($request->only('username'));
    }

    /**
     * Log out the admin.
     */
    public function logout(Request $request)
    {
        Session::forget(['is_admin_logged_in', 'admin_username']);
        
        return redirect()->route('admin.login')
            ->with('success', 'Başarıyla çıkış yaptınız.');
    }
}
