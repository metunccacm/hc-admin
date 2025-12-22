<?php

namespace App\Http\Controllers;

use App\Services\SupabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    protected SupabaseService $supabase;

    public function __construct(SupabaseService $supabase)
    {
        $this->supabase = $supabase;
    }

    /**
     * Display the admin dashboard with all restaurants.
     */
    public function dashboard()
    {
        $restaurants = $this->supabase->getRestaurants();
        
        // Add ratings to each restaurant
        foreach ($restaurants as &$restaurant) {
            $restaurant['average_rating'] = $this->supabase->getAverageRating($restaurant['id']);
            $restaurant['review_count'] = $this->supabase->getReviewCount($restaurant['id']);
        }

        return view('admin.dashboard', compact('restaurants'));
    }

    /**
     * Show the create restaurant form.
     */
    public function create()
    {
        return view('admin.restaurants.create');
    }

    /**
     * Store a new restaurant.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50',
            'password' => 'required|string|min:6|max:100',
            'description' => 'nullable|string|max:1000',
            'phone_number' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Check if username already exists
        $existingRestaurant = $this->supabase->getRestaurantByUsername($request->input('username'));
        if ($existingRestaurant) {
            return back()->withErrors(['username' => 'Bu kullanıcı adı zaten kullanılıyor.'])->withInput();
        }

        $restaurantData = [
            'id' => Str::uuid()->toString(),
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'description' => $request->input('description'),
            'phone_number' => $request->input('phone_number'),
            'working_hours' => json_encode([]),
            'menu_urls' => [],
        ];

        $restaurant = $this->supabase->createRestaurant($restaurantData);

        if ($restaurant) {
            // Handle logo upload after creation
            if ($request->hasFile('logo')) {
                $restaurantName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $restaurant['name']);
                $logoUrl = $this->supabase->uploadFile($request->file('logo'), "{$restaurantName}/logo");
                if ($logoUrl) {
                    $this->supabase->updateRestaurant($restaurant['id'], ['logo_url' => $logoUrl]);
                }
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Restoran başarıyla oluşturuldu.');
        }

        return back()->withErrors(['error' => 'Restoran oluşturulurken bir hata oluştu.'])
            ->withInput();
    }

    /**
     * Show the edit restaurant form.
     */
    public function edit(string $id)
    {
        $restaurant = $this->supabase->getRestaurant($id);
        
        if (!$restaurant) {
            return redirect()->route('admin.dashboard')
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
                } elseif (is_string($value) && preg_match('/(\d{2}:\d{2})\s*-\s*(\d{2}:\d{2})/', $value, $matches)) {
                    $workingHoursForForm[$day] = ['open' => $matches[1], 'close' => $matches[2], 'closed' => false];
                } else {
                    $workingHoursForForm[$day] = ['open' => '', 'close' => '', 'closed' => false];
                }
            } else {
                $workingHoursForForm[$day] = ['open' => '', 'close' => '', 'closed' => false];
            }
        }

        // Parse menu URLs
        $menuUrls = $restaurant['menu_urls'] ?? [];
        if (is_string($menuUrls)) {
            $menuUrls = json_decode($menuUrls, true) ?? [];
        }

        $averageRating = $this->supabase->getAverageRating($id);
        $reviewCount = $this->supabase->getReviewCount($id);

        return view('admin.restaurants.edit', compact('restaurant', 'workingHoursForForm', 'menuUrls', 'averageRating', 'reviewCount'));
    }

    /**
     * Update a restaurant.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50',
            'password' => 'nullable|string|min:6|max:100',
            'description' => 'nullable|string|max:1000',
            'phone_number' => 'nullable|string|max:20',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'menus.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'working_hours' => 'nullable|array',
        ]);

        $restaurant = $this->supabase->getRestaurant($id);

        // Check if username already exists (excluding current restaurant)
        $existingRestaurant = $this->supabase->getRestaurantByUsername($request->input('username'));
        if ($existingRestaurant && $existingRestaurant['id'] !== $id) {
            return back()->withErrors(['username' => 'Bu kullanıcı adı zaten kullanılıyor.'])->withInput();
        }

        if (!$restaurant) {
            return redirect()->route('admin.dashboard')
                ->withErrors(['error' => 'Restoran bulunamadı.']);
        }

        $updateData = [
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'description' => $request->input('description'),
            'phone_number' => $request->input('phone_number'),
        ];

        // Update password only if provided
        if ($request->filled('password')) {
            $updateData['password'] = $request->input('password');
        }

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
        $updateData['working_hours'] = $workingHours;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $restaurantName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $request->input('name'));
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

            $restaurantName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $request->input('name'));
            foreach ($request->file('menus') as $menu) {
                $menuUrl = $this->supabase->uploadFile($menu, "{$restaurantName}/menu");
                if ($menuUrl) {
                    $existingMenus[] = $menuUrl;
                }
            }
            $updateData['menu_urls'] = $existingMenus;
        }

        $success = $this->supabase->updateRestaurant($id, $updateData);

        if ($success) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Restoran başarıyla güncellendi.');
        }

        return back()->withErrors(['error' => 'Restoran güncellenirken bir hata oluştu.']);
    }

    /**
     * Delete a restaurant.
     */
    public function destroy(string $id)
    {
        $success = $this->supabase->deleteRestaurant($id);

        if ($success) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Restoran başarıyla silindi.');
        }

        return back()->withErrors(['error' => 'Restoran silinirken bir hata oluştu.']);
    }

    /**
     * Remove a menu file from a restaurant.
     */
    public function removeMenu(Request $request, string $id)
    {
        $request->validate([
            'menu_url' => 'required|string',
        ]);

        $restaurant = $this->supabase->getRestaurant($id);

        if (!$restaurant) {
            return response()->json(['error' => 'Restoran bulunamadı.'], 404);
        }

        $menuUrls = $restaurant['menu_urls'] ?? [];
        if (is_string($menuUrls)) {
            $menuUrls = json_decode($menuUrls, true) ?? [];
        }

        $menuUrl = $request->input('menu_url');
        $menuUrls = array_filter($menuUrls, fn($url) => $url !== $menuUrl);

        $success = $this->supabase->updateRestaurant($id, [
            'menu_urls' => array_values($menuUrls),
        ]);

        if ($success) {
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Menü silinirken bir hata oluştu.'], 500);
    }
}
