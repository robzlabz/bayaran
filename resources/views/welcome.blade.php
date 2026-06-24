<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bayaran</title>
    <script>
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex flex-col">

        {{-- Main Content --}}
        <main class="flex-1 flex items-center justify-center px-4 py-12">
            <div class="w-full max-w-md mx-auto text-center">

                {{-- Logo --}}
                <div class="mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-2xl shadow-lg mb-4">
                        <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold">Bayaran</h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manajemen karyawan & absensi</p>
                </div>

                {{-- Login Cards --}}
                <div class="space-y-4">

                    <a href="{{ route('login') }}"
                       class="block w-full p-5 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:shadow-md transition-all duration-200 text-center">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-indigo-100 dark:bg-indigo-900 mb-3">
                            <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                        </div>
                        <p class="text-base font-semibold text-gray-900 dark:text-gray-100">Masuk ke Bayaran</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Karyawan — No HP | Admin — Email</p>
                    </a>

                </div>

                {{-- Register link --}}
                <p class="mt-8 text-sm text-gray-500 dark:text-gray-400">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">Daftar</a>
                </p>
            </div>
        </main>

        {{-- Footer --}}
        <footer class="py-6 text-center text-xs text-gray-400 dark:text-gray-500">
            Bayaran
        </footer>

    </div>
</body>
</html>
