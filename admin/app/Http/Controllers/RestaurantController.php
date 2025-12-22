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
        $reviews = $this->supabase->getRestaurantReviews($restaurantId);
        
        // Log for debugging
        \Log::info('Restaurant Dashboard Data', [
            'restaurant_id' => $restaurantId,
            'review_count' => $reviewCount,
            'average_rating' => $averageRating,
            'reviews_fetched' => count($reviews)
        ]);

        // Parse working hours if it's a JSON string
        $workingHours = $restaurant['working_hours'] ?? null;
        if (is_string($workingHours)) {
            $workingHours = json_decode($workingHours, true);
        }

        // Parse menu URLs if it's an array or string
        $menuUrls = $restaurant['menu_urls'] ?? [];
        if (is_string($menuUrls)) {
            $menuUrls = json_decode($menuUrls, true) ?? [];
        }
        // Ensure it's always an array
        if (!is_array($menuUrls)) {
            $menuUrls = [];
        }

        return view('restaurant.dashboard', compact('restaurant', 'averageRating', 'reviewCount', 'reviews', 'workingHours', 'menuUrls'));
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

        // Parse working hours if it's a JSON string and convert to form-friendly format
        $workingHours = $restaurant['working_hours'] ?? null;
        if (is_string($workingHours)) {
            $workingHours = json_decode($workingHours, true);
        }
        
        // Convert working hours from Supabase format to form format
        $workingHoursForForm = [];
        $days = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
        foreach ($days as $day) {
            if (isset($workingHours[$day])) {
                $value = $workingHours[$day];
                // Handle old nested format (array with open/close/closed)
                if (is_array($value)) {
                    $workingHoursForForm[$day] = [
                        'open' => $value['open'] ?? '',
                        'close' => $value['close'] ?? '',
                        'closed' => $value['closed'] ?? false
                    ];
                }
                // Handle new string format
                elseif (is_string($value) && ($value === 'Kapalı' || strtolower($value) === 'kapalı')) {
                    $workingHoursForForm[$day] = ['open' => '', 'close' => '', 'closed' => true];
                } elseif (is_string($value) && preg_match('/^(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})$/', $value, $matches)) {
                    $workingHoursForForm[$day] = ['open' => $matches[1], 'close' => $matches[2], 'closed' => false];
                } else {
                    $workingHoursForForm[$day] = ['open' => '', 'close' => '', 'closed' => false];
                }
            } else {
                $workingHoursForForm[$day] = ['open' => '', 'close' => '', 'closed' => false];
            }
        }

        // Parse menu URLs if it's an array or string
        $menuUrls = $restaurant['menu_urls'] ?? [];
        if (is_string($menuUrls)) {
            $menuUrls = json_decode($menuUrls, true) ?? [];
        }
        // Ensure it's always an array
        if (!is_array($menuUrls)) {
            $menuUrls = [];
        }

        return view('restaurant.edit', compact('restaurant', 'workingHoursForForm', 'menuUrls'));
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

        // Handle working hours - Convert to Supabase format (simple strings)
        $workingHours = [];
        $days = ['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'];
        $workingHoursInput = $request->input('working_hours', []);
        
        foreach ($days as $index => $day) {
            $dayHours = $workingHoursInput[$index] ?? [];
            $isClosed = isset($dayHours['closed']) && $dayHours['closed'] === '1';
            
            if ($isClosed) {
                $workingHours[$day] = 'Kapalı';
            } else {
                $open = $dayHours['open'] ?? '';
                $close = $dayHours['close'] ?? '';
                if ($open && $close) {
                    $workingHours[$day] = "{$open} - {$close}";
                } else {
                    $workingHours[$day] = 'Kapalı';
                }
            }
        }
        // Note: working_hours is sent as an array, Laravel HTTP client will handle JSON encoding
        $updateData['working_hours'] = $workingHours;

        // Log the update data for debugging
        \Log::info('Updating restaurant', [
            'restaurant_id' => $restaurantId,
            'update_data' => $updateData
        ]);

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

        \Log::error('Restaurant update failed', [
            'restaurant_id' => $restaurantId,
            'update_data' => $updateData
        ]);
        
        return back()->withErrors(['error' => 'Profil güncellenirken bir hata oluştu. Lütfen Supabase politikalarını kontrol edin.']);
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
