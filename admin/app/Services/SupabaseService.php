<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;

class SupabaseService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $storageBucket;

    public function __construct()
    {
        $this->apiUrl = config('services.supabase.api_url');
        $this->apiKey = config('services.supabase.api_key');
        $this->storageBucket = config('services.supabase.storage_bucket', 'delivery');
    }

    /**
     * Get all restaurants
     */
    public function getRestaurants(): array
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get("{$this->apiUrl}/rest/v1/restaurants", [
                'select' => '*',
                'order' => 'created_at.desc'
            ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Get a single restaurant by ID
     */
    public function getRestaurant(string $id): ?array
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get("{$this->apiUrl}/rest/v1/restaurants", [
                'id' => "eq.{$id}",
                'select' => '*'
            ]);

        $data = $response->successful() ? $response->json() : [];
        return !empty($data) ? $data[0] : null;
    }

    /**
     * Get restaurant by username (for login)
     */
    public function getRestaurantByUsername(string $username): ?array
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get("{$this->apiUrl}/rest/v1/restaurants", [
                'username' => "eq.{$username}",
                'select' => '*'
            ]);

        $data = $response->successful() ? $response->json() : [];
        return !empty($data) ? $data[0] : null;
    }

    /**
     * Create a new restaurant
     */
    public function createRestaurant(array $data): ?array
    {
        $response = Http::withHeaders(array_merge($this->getHeaders(), [
            'Prefer' => 'return=representation'
        ]))->post("{$this->apiUrl}/rest/v1/restaurants", $data);

        $result = $response->successful() ? $response->json() : null;
        return is_array($result) && !empty($result) ? $result[0] : $result;
    }

    /**
     * Update a restaurant
     */
    public function updateRestaurant(string $id, array $data): bool
    {
        $response = Http::withHeaders($this->getHeaders())
            ->patch("{$this->apiUrl}/rest/v1/restaurants?id=eq.{$id}", $data);

        return $response->successful();
    }

    /**
     * Delete a restaurant
     */
    public function deleteRestaurant(string $id): bool
    {
        $response = Http::withHeaders($this->getHeaders())
            ->delete("{$this->apiUrl}/rest/v1/restaurants?id=eq.{$id}");

        return $response->successful();
    }

    /**
     * Get restaurant reviews
     */
    public function getRestaurantReviews(string $restaurantId): array
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get("{$this->apiUrl}/rest/v1/restaurant_reviews", [
                'restaurant_id' => "eq.{$restaurantId}",
                'select' => '*',
                'order' => 'created_at.desc'
            ]);

        return $response->successful() ? $response->json() : [];
    }

    /**
     * Get average rating for a restaurant
     */
    public function getAverageRating(string $restaurantId): float
    {
        $reviews = $this->getRestaurantReviews($restaurantId);
        
        if (empty($reviews)) {
            return 0.0;
        }

        $totalRating = array_sum(array_column($reviews, 'rating'));
        return round($totalRating / count($reviews), 1);
    }

    /**
     * Get review count for a restaurant
     */
    public function getReviewCount(string $restaurantId): int
    {
        $reviews = $this->getRestaurantReviews($restaurantId);
        return count($reviews);
    }

    /**
     * Upload file to Supabase Storage
     */
    public function uploadFile(UploadedFile $file, string $path): ?string
    {
        $fileName = time() . '_' . $file->getClientOriginalName();
        $fullPath = "{$path}/{$fileName}";

        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => $file->getMimeType(),
        ])->withBody(
            file_get_contents($file->getRealPath()),
            $file->getMimeType()
        )->post("{$this->apiUrl}/storage/v1/object/{$this->storageBucket}/{$fullPath}");

        if ($response->successful()) {
            return "{$this->apiUrl}/storage/v1/object/public/{$this->storageBucket}/{$fullPath}";
        }

        return null;
    }

    /**
     * Delete file from Supabase Storage
     */
    public function deleteFile(string $path): bool
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
        ])->delete("{$this->apiUrl}/storage/v1/object/{$this->storageBucket}/{$path}");

        return $response->successful();
    }

    /**
     * Get headers for API requests
     */
    protected function getHeaders(): array
    {
        return [
            'apikey' => $this->apiKey,
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
