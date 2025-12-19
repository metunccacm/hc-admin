@extends('layouts.app')

@section('title', 'Restoran Girişi')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-orange-400 via-red-500 to-pink-500 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-gradient-to-r from-orange-500 to-red-500 rounded-full flex items-center justify-center mb-4">
                    <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Restoran Girişi</h2>
                <p class="mt-2 text-sm text-gray-600">Hesabınıza giriş yapın</p>
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

            <form class="mt-8 space-y-6" action="{{ route('restaurant.login.submit') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Kullanıcı Adı</label>
                        <input id="username" name="username" type="text" required 
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200"
                            placeholder="Kullanıcı adınızı girin" value="{{ old('username') }}">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Şifre</label>
                        <input id="password" name="password" type="password" required 
                            class="appearance-none relative block w-full px-4 py-3 border border-gray-300 placeholder-gray-400 text-gray-900 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200"
                            placeholder="Şifrenizi girin">
                    </div>
                </div>

                <button type="submit" 
                    class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-lg text-white bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition duration-200 transform hover:scale-[1.02]">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-orange-200 group-hover:text-orange-100" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                        </svg>
                    </span>
                    Giriş Yap
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('admin.login') }}" class="text-sm text-gray-600 hover:text-orange-500 transition duration-200">
                    Admin girişi için tıklayın
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
