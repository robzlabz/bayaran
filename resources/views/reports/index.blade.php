<x-app-layout>
    <x-slot name="header">Laporan</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            <x-dashboard-stat icon="people" label="Karyawan" :value="$activeEmployees . ' / ' . $totalEmployees" color="indigo" />
            <x-dashboard-stat icon="clipboard" label="Absen Hari Ini" :value="$todayAttendance" color="yellow" />
            <x-dashboard-stat icon="money" label="Hutang Aktif" :value="'Rp ' . number_format($unpaidDebt, 0, ',', '.')" color="red" />
            <x-dashboard-stat icon="wallet" label="Total Saldo" :value="'Rp ' . number_format($totalBalance, 0, ',', '.')" color="green" />
            <x-dashboard-stat icon="calendar" label="Izin Tahun Ini" :value="$totalLeaves" color="blue" />
        </div>

        {{-- Report Links --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Attendance Report --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                <a href="{{ route('company.reports.attendance') }}" class="block p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-900 flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rekap Absensi</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Lihat & export rekap absensi per bulan</p>
                        </div>
                    </div>
                    <div class="flex gap-2 text-sm">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-gray-600 dark:text-gray-400">Filter per karyawan</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-gray-600 dark:text-gray-400">Total jam kerja</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-gray-600 dark:text-gray-400">Download PDF</span>
                    </div>
                </a>
            </div>

            {{-- Debt Report --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                <a href="{{ route('company.reports.debts') }}" class="block p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 rounded-xl bg-red-100 dark:bg-red-900 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rekap Hutang</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Lihat & export rekap hutang per bulan</p>
                        </div>
                    </div>
                    <div class="flex gap-2 text-sm">
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-gray-600 dark:text-gray-400">Filter status</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-gray-600 dark:text-gray-400">Total per periode</span>
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded text-gray-600 dark:text-gray-400">Download PDF</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
