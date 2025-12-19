@extends('layouts.app')

@section('title', 'Yeni Restoran Ekle')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-gray-800 to-gray-900 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center text-gray-300 hover:text-white transition duration-200">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Geri
                    </a>
                </div>
                <h1 class="text-xl font-bold text-white">Yeni Restoran Ekle</h1>
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

        <form action="{{ route('admin.restaurants.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Basic Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <svg class="h-5 w-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Temel Bilgiler
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Restoran Adı *</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition duration-200"
                            placeholder="Restoran adını girin">
                    </div>

                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı *</label>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition duration-200"
                            placeholder="Giriş için kullanıcı adı">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Şifre *</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition duration-200"
                            placeholder="Giriş için şifre (min. 6 karakter)">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Açıklama</label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition duration-200"
                            placeholder="Restoran hakkında kısa bir açıklama">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Telefon Numarası</label>
                        <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition duration-200"
                            placeholder="+90 5XX XXX XX XX">
                    </div>

                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                        <input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition duration-200">
                        <p class="mt-1 text-sm text-gray-500">JPG, JPEG veya PNG formatında, maksimum 2MB</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.dashboard') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition duration-200">
                    İptal
                </a>
                <button type="submit" class="px-6 py-3 bg-gray-900 text-white font-medium rounded-lg hover:bg-gray-800 transition duration-200">
                    Restoran Oluştur
                </button>
            </div>
        </form>
    </main>
</div>
@endsection
