<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RestaurantController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Display the restaurant dashboard.
     */
    public function dashboard()
    {
        $restaurantId = Session::get('restaurant_id');
        $restaurant = $this->supabase->getRestaurant($restaurantId);
        
        if (!$restaurant) {
            return redirect()->route('restaurant.login')
                ->withErrors(['error' => 'Restoran bulunamadı.']);
        }

        $averageRating = $this->supabase->getAverageRating($restaurantId);
        $reviewCount = $this->supabase->getReviewCount($restaurantId);

        // Parse working hours if it's a JSON string
        $workingHours = $restaurant['working_hours'] ?? null;
        if (is_string($workingHours)) {
            $workingHours = json_decode($workingHours, true);
        }

        // Parse menu URLs if it's an array
        $menuUrls = $restaurant['menu_urls'] ?? [];
        if (is_string($menuUrls)) {
            $menuUrls = json_decode($menuUrls, true) ?? [];
        }

        return view('restaurant.dashboard', compact('restaurant', 'averageRating', 'reviewCount', 'workingHours', 'menuUrls'));
    }

    /**
     * Show the edit profile form.
     */
    public function edit()
    {
        $restaurantId = Session::get('restaurant_id');
        $restaurant = $this->supabase->getRestaurant($restaurantId);
        
        if (!$restaurant) {
            return redirect()->route('restaurant.login')
                ->withErrors(['error' => 'Restoran bulunamadı.']);
        }

        // Parse working hours if it's a JSON string
        $workingHours = $restaurant['working_hours'] ?? null;
        if (is_string($workingHours)) {
            $workingHours = json_decode($workingHours, true);
        }

        // Parse menu URLs if it's an array
        $menuUrls = $restaurant['menu_urls'] ?? [];
        if (is_string($menuUrls)) {
            $menuUrls = json_decode($menuUrls, true) ?? [];
        }

        return view('restaurant.edit', compact('restaurant', 'workingHours', 'menuUrls'));
    }

    /**
     * Update the restaurant profile.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'phone_number' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'menus.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'working_hours' => 'nullable|array',
            'working_hours.*.open' => 'nullable|string',
            'working_hours.*.close' => 'nullable|string',
            'working_hours.*.closed' => 'nullable|boolean',
        ]);

        $restaurantId = Session::get('restaurant_id');
        $restaurant = $this->supabase->getRestaurant($restaurantId);

        if (!$restaurant) {
            return redirect()->route('restaurant.login')
                ->withErrors(['error' => 'Restoran bulunamadı.']);
        }

        $updateData = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'phone_number' => $request->input('phone_number'),
        ];

        // Handle working hours
        $workingHours = [];
        $days = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
        $workingHoursInput = $request->input('working_hours', []);
        
        foreach ($days as $index => $day) {
            $dayHours = $workingHoursInput[$index] ?? [];
            $workingHours[$day] = [
                'open' => $dayHours['open'] ?? '',
                'close' => $dayHours['close'] ?? '',
                'closed' => isset($dayHours['closed']) && $dayHours['closed'] === '1',
            ];
        }
        $updateData['working_hours'] = json_encode($workingHours);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $restaurantName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $restaurant['name']);
            $logoUrl = $this->supabase->uploadFile($request->file('logo'), "{$restaurantName}/logo");
            if ($logoUrl) {
                $updateData['logo_url'] = $logoUrl;
            }
        }

        // Handle menu uploads
        if ($request->hasFile('menus')) {
            $existingMenus = $restaurant['menu_urls'] ?? [];
            if (is_string($existingMenus)) {
                $existingMenus = json_decode($existingMenus, true) ?? [];
            }

            $restaurantName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $restaurant['name']);
            foreach ($request->file('menus') as $menu) {
                $menuUrl = $this->supabase->uploadFile($menu, "{$restaurantName}/menu");
                if ($menuUrl) {
                    $existingMenus[] = $menuUrl;
                }
            }
            $updateData['menu_urls'] = $existingMenus;
        }

        $success = $this->supabase->updateRestaurant($restaurantId, $updateData);

        if ($success) {
            // Update session restaurant name if changed
            Session::put('restaurant_name', $request->input('name'));
            
            return redirect()->route('restaurant.dashboard')
                ->with('success', 'Profil başarıyla güncellendi.');
        }

        return back()->withErrors(['error' => 'Profil güncellenirken bir hata oluştu.']);
    }

    /**
     * Remove a menu file.
     */
    public function removeMenu(Request $request)
    {
        $request->validate([
            'menu_url' => 'required|string',
        ]);

        $restaurantId = Session::get('restaurant_id');
        $restaurant = $this->supabase->getRestaurant($restaurantId);

        if (!$restaurant) {
            return response()->json(['error' => 'Restoran bulunamadı.'], 404);
        }

        $menuUrls = $restaurant['menu_urls'] ?? [];
        if (is_string($menuUrls)) {
            $menuUrls = json_decode($menuUrls, true) ?? [];
        }

        $menuUrl = $request->input('menu_url');
        $menuUrls = array_filter($menuUrls, fn($url) => $url !== $menuUrl);

        $success = $this->supabase->updateRestaurant($restaurantId, [
            'menu_urls' => array_values($menuUrls),
        ]);

        if ($success) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Menü silinirken bir hata oluştu.'], 500);
    }
}
