@extends('layouts.app')

@section('title', 'Admin Girişi')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-800 via-gray-900 to-black py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-gradient-to-r from-gray-700 to-gray-900 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Admin Girişi</h2>
                <p class="mt-2 text-sm text-gray-600">Yönetim paneline erişin</p>
            </div>

            @if(session('success'))
                <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form class="mt-8 space-y-6" action="{{ route('admin.login.submit') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı</label>
                        <input id="username" name="username" type="text" required 
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition duration-200"
                            placeholder="Admin kullanıcı adı" value="{{ old('username') }}">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                        <input id="password" name="password" type="password" required 
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition duration-200"
                            placeholder="Admin şifresi">
                    </div>
                </div>

                <button type="submit" 
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-gray-700 to-gray-900 hover:from-gray-800 hover:to-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200 transform hover:scale-[1.02]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400 group-hover:text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    Admin Girişi
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('restaurant.login') }}" class="text-sm text-gray-600 hover:text-gray-800 transition duration-200">
                    Restoran girişi için tıklayın
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
