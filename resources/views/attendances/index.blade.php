<x-app-layout>
    <x-slot name="header">Absensi</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-300">{!! session('success') !!}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Absen Hari Ini</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $todayCount }} / {{ $activeEmployees }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tanggal</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div class="flex items-center gap-2">
                <a href="{{ route('company.attendances.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Input Manual
                </a>
            </div>
            <form method="GET" class="flex items-center gap-2">
                <select name="employee_id" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm px-3 py-2">
                    <option value="">Semua karyawan</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                       class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm px-3 py-2">
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Karyawan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Clock In</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Clock Out</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Jam Kerja</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($attendances as $a)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-300 shrink-0">
                                            {{ substr($a->employee->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $a->employee->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100 font-medium">{{ $a->clock_in->format('H:i') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $a->clock_out?->format('H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $a->work_hours ? $a->work_hours . ' jam' : '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($a->is_manual_entry)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">Manual</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Foto</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-400 max-w-[150px] truncate">{{ $a->notes ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="font-medium">Belum ada absensi</p>
                                    <p class="mt-1 text-sm">Tidak ada data absensi untuk tanggal ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
