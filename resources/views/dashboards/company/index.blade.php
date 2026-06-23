<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-medium">Selamat datang, {{ Auth::user()->name }}!</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if (Auth::user()->account_type === 'company')
                        Akun Company — {{ Auth::user()->company_name }}
                    @else
                        Akun Personal
                    @endif
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <x-dashboard-stat icon="people" label="Total Karyawan" :value="$totalEmployees" color="indigo" />
            <x-dashboard-stat icon="money" label="Hutang Aktif" :value="'Rp ' . number_format($totalDebts, 0, ',', '.')" color="red" />
            <x-dashboard-stat icon="wallet" label="Total Saldo" :value="'Rp ' . number_format($totalBalance, 0, ',', '.')" color="green" />
            <x-dashboard-stat icon="clipboard" label="Absen Hari Ini" :value="$todayAttendance" color="yellow" />
        </div>

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Aksi Cepat</h3>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <a href="{{ route('company.employees.index') }}" class="inline-flex items-center justify-center px-4 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 text-center">
                        + Kelola Karyawan
                    </a>
                    <a href="{{ route('company.debts.create') }}" class="inline-flex items-center justify-center px-4 py-3 bg-green-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 text-center">
                        + Catat Hutang
                    </a>
                    <a href="#" class="inline-flex items-center justify-center px-4 py-3 bg-yellow-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 text-center">
                        Lihat Absensi
                    </a>
                    <a href="#" class="inline-flex items-center justify-center px-4 py-3 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 text-center">
                        + Top Up Saldo
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
