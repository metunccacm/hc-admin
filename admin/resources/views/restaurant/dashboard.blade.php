@extends('layouts.app')

@section('title', 'Restoran Paneli - ' . $restaurant['name'])

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    @if($restaurant['logo_url'] ?? false)
                        <img src="{{ $restaurant['logo_url'] }}" alt="{{ $restaurant['name'] }}" class="h-12 w-12 rounded-full object-cover border-2 border-orange-500">
                    @else
                        <div class="h-12 w-12 rounded-full bg-gradient-to-r from-orange-500 to-red-500 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ substr($restaurant['name'], 0, 1) }}</span>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">{{ $restaurant['name'] }}</h1>
                        <p class="text-sm text-gray-500">Restoran Paneli</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('restaurant.edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-200">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Profili Düzenle
                    </a>
                    <form action="{{ route('restaurant.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-500 hover:bg-red-600 transition duration-200">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Çıkış Yap
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Rating Card -->
        <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl shadow-lg p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-medium opacity-90">Ortalama Puan</h2>
                    <div class="flex items-center mt-2">
                        <span class="text-5xl font-bold">{{ number_format($averageRating, 1) }}</span>
                        <span class="text-2xl ml-1">/5</span>
                    </div>
                    <p class="mt-2 opacity-80">{{ $reviewCount }} değerlendirme</p>
                </div>
                <div class="flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="h-8 w-8 {{ $i <= round($averageRating) ? 'text-yellow-300' : 'text-white/30' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    @endfor
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Restaurant Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Restoran Bilgileri
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Restoran Adı</label>
                        <p class="mt-1 text-gray-900">{{ $restaurant['name'] }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">Açıklama</label>
                        <p class="mt-1 text-gray-900">{{ $restaurant['description'] ?? 'Henüz açıklama eklenmemiş' }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-500">Telefon / WhatsApp</label>
                        <div class="mt-1 flex items-center space-x-3">
                            <p class="text-gray-900">{{ $restaurant['phone_number'] ?? 'Belirtilmemiş' }}</p>
                            @if($restaurant['phone_number'] ?? false)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $restaurant['phone_number']) }}" target="_blank" 
                                   class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-sm rounded-full hover:bg-green-600 transition duration-200">
                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                    </svg>
                                    WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Working Hours -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Çalışma Saatleri
                </h3>
                
                @if($workingHours && is_array($workingHours))
                    <div class="space-y-3">
                        @foreach(['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'] as $day)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                <span class="font-medium text-gray-700">{{ $day }}</span>
                                @if(isset($workingHours[$day]))
                                    @if($workingHours[$day] === 'Kapalı' || strtolower($workingHours[$day]) === 'kapalı')
                                        <span class="text-red-500 font-medium">Kapalı</span>
                                    @else
                                        <span class="text-gray-900">{{ $workingHours[$day] }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">Belirtilmemiş</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">Çalışma saatleri henüz belirlenmemiş.</p>
                @endif
            </div>
        </div>

        <!-- Menus Section -->
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Menüler
            </h3>
            
            @if($menuUrls && count($menuUrls) > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($menuUrls as $index => $menuUrl)
                        <a href="{{ $menuUrl }}" target="_blank" class="group relative aspect-square bg-gray-100 rounded-lg overflow-hidden hover:ring-2 hover:ring-orange-500 transition duration-200">
                            @if(str_ends_with(strtolower($menuUrl), '.pdf'))
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <svg class="h-16 w-16 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="absolute bottom-2 left-2 right-2 text-center text-sm font-medium text-gray-700 bg-white/80 rounded px-2 py-1">PDF Menü {{ $index + 1 }}</span>
                            @else
                                <img src="{{ $menuUrl }}" alt="Menü {{ $index + 1 }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition duration-200 flex items-center justify-center">
                                <svg class="h-8 w-8 text-white opacity-0 group-hover:opacity-100 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Henüz menü eklenmemiş.</p>
            @endif
        </div>

        <!-- Reviews Section -->
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="h-5 w-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                Müşteri Değerlendirmeleri ({{ $reviewCount }})
            </h3>
            
            @if($reviews && count($reviews) > 0)
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <h4 class="font-semibold text-gray-900">Kullanıcı {{ substr($review['user_id'] ?? 'Anonim', 0, 8) }}</h4>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="h-4 w-4 {{ $i <= ($review['rating'] ?? 0) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                            @endfor
                                            <span class="ml-1 text-sm text-gray-600">({{ $review['rating'] ?? 0 }})</span>
                                        </div>
                                    </div>
                                    @if(isset($review['review_text']) && $review['review_text'])
                                        <p class="text-gray-700 text-sm">{{ $review['review_text'] }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500 ml-4">
                                    {{ isset($review['created_at']) ? \Carbon\Carbon::parse($review['created_at'])->format('d.m.Y') : '' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Henüz değerlendirme yapılmamış.</p>
            @endif
        </div>
    </main>
</div>
@endsection
