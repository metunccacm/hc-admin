@extends('layouts.app')

@section('title', 'Profil Düzenle - ' . $restaurant['name'])

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('restaurant.dashboard') }}" class="flex items-center text-gray-600 hover:text-gray-900 transition duration-200">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Panele Dön
                    </a>
                </div>
                <h1 class="text-xl font-bold text-gray-900">Profil Düzenle</h1>
                <div></div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($errors->any())
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('restaurant.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Basic Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Temel Bilgiler
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Restoran Adı *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $restaurant['name']) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200">{{ old('description', $restaurant['description']) }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Telefon / WhatsApp Numarası</label>
                        <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $restaurant['phone_number']) }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200"
                            placeholder="+90 5XX XXX XX XX">
                        <p class="mt-1 text-sm text-gray-500">Bu numara hem telefon hem de WhatsApp için kullanılacaktır.</p>
                    </div>
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Logo
                </h3>

                <div class="flex items-start space-x-6">
                    @if($restaurant['logo_url'] ?? false)
                        <img src="{{ $restaurant['logo_url'] }}" alt="Current Logo" class="h-24 w-24 rounded-lg object-cover border-2 border-gray-200">
                    @else
                        <div class="h-24 w-24 rounded-lg bg-gray-100 flex items-center justify-center border-2 border-dashed border-gray-300">
                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    <div class="flex-1">
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">Yeni Logo Yükle</label>
                        <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200">
                        <p class="mt-2 text-sm text-gray-500">JPG, JPEG veya PNG formatında, maksimum 2MB</p>
                    </div>
                </div>
            </div>

            <!-- Working Hours -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Çalışma Saatleri
                </h3>

                <div class="space-y-4">
                    @foreach(['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'] as $index => $day)
                        @php
                            $dayHours = $workingHours[$day] ?? ['open' => '', 'close' => '', 'closed' => false];
                        @endphp
                        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                            <span class="w-28 font-medium text-gray-700">{{ $day }}</span>
                            
                            <div class="flex items-center space-x-2 flex-1">
                                <input type="time" name="working_hours[{{ $index }}][open]" 
                                    value="{{ $dayHours['open'] ?? '' }}"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200 working-hours-time"
                                    data-day="{{ $index }}">
                                <span class="text-gray-500">-</span>
                                <input type="time" name="working_hours[{{ $index }}][close]" 
                                    value="{{ $dayHours['close'] ?? '' }}"
                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200 working-hours-time"
                                    data-day="{{ $index }}">
                            </div>

                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="working_hours[{{ $index }}][closed]" value="1"
                                    {{ ($dayHours['closed'] ?? false) ? 'checked' : '' }}
                                    class="w-4 h-4 text-red-500 border-gray-300 rounded focus:ring-red-500 closed-checkbox"
                                    data-day="{{ $index }}">
                                <span class="text-sm text-gray-600">Kapalı</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Menu Upload -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Menüler
                </h3>

                @if($menuUrls && count($menuUrls) > 0)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Mevcut Menüler</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($menuUrls as $index => $menuUrl)
                                <div class="relative group">
                                    <a href="{{ $menuUrl }}" target="_blank" class="block aspect-square bg-gray-100 rounded-lg overflow-hidden">
                                        @if(str_ends_with(strtolower($menuUrl), '.pdf'))
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <svg class="h-12 w-12 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @else
                                            <img src="{{ $menuUrl }}" alt="Menü" class="w-full h-full object-cover">
                                        @endif
                                    </a>
                                    <button type="button" onclick="removeMenu('{{ $menuUrl }}')"
                                        class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition duration-200 hover:bg-red-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div>
                    <label for="menus" class="block text-sm font-medium text-gray-700 mb-2">Yeni Menü Ekle</label>
                    <input type="file" name="menus[]" id="menus" multiple accept="image/jpeg,image/png,image/jpg,application/pdf"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200">
                    <p class="mt-2 text-sm text-gray-500">JPG, JPEG, PNG veya PDF formatında, maksimum 5MB</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('restaurant.dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition duration-200">
                    İptal
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white font-medium rounded-lg hover:from-orange-600 hover:to-red-600 transition duration-200">
                    Kaydet
                </button>
            </div>
        </form>
    </main>
</div>

@push('scripts')
<script>
    // Toggle time inputs when closed checkbox is checked
    document.querySelectorAll('.closed-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const day = this.dataset.day;
            const timeInputs = document.querySelectorAll(`.working-hours-time[data-day="${day}"]`);
            timeInputs.forEach(input => {
                input.disabled = this.checked;
                input.classList.toggle('opacity-50', this.checked);
            });
        });
        
        // Initialize on page load
        if (checkbox.checked) {
            const day = checkbox.dataset.day;
            const timeInputs = document.querySelectorAll(`.working-hours-time[data-day="${day}"]`);
            timeInputs.forEach(input => {
                input.disabled = true;
                input.classList.add('opacity-50');
            });
        }
    });

    function removeMenu(menuUrl) {
        if (confirm('Bu menüyü silmek istediğinizden emin misiniz?')) {
            fetch('{{ route("restaurant.remove-menu") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ menu_url: menuUrl })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Bir hata oluştu');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Bir hata oluştu');
            });
        }
    }
</script>
@endpush
@endsection
