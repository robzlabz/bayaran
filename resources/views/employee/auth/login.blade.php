<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} — Absensi Karyawan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-indigo-50 to-blue-100 dark:from-gray-900 dark:to-gray-800 p-4">
        <!-- Logo -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-2xl shadow-lg mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Absensi Karyawan</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Clock in / Clock out dengan mudah</p>
        </div>

        <!-- Login Card -->
        <div class="w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
            <form method="POST" action="{{ route('employee.login') }}">
                @csrf

                <!-- Phone -->
                <div class="mb-5">
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nomor HP</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required autofocus autocomplete="phone"
                           placeholder="0812xxxxxxxx"
                           class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           placeholder="Masukkan password"
                           class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-3 @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit -->
                <button type="submit" class="w-full py-3 px-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm transition duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 text-sm">
                    Masuk
                </button>

                @if (Route::has('login'))
                    <div class="mt-4 text-center">
                        <a href="{{ route('login') }}" class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            Login untuk Admin / Perusahaan →
                        </a>
                    </div>
                @endif
            </form>
        </div>

        <!-- Footer -->
        <p class="mt-8 text-xs text-gray-400 dark:text-gray-500">KaryawanKu &mdash; Manajemen Karyawan</p>
    </div>
</body>
</html>
