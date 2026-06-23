<x-app-layout>
    <x-slot name="header">Rekap Absensi</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-300">{!! session('success') !!}</div>
        @endif

        {{-- Filters --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <form method="GET" class="p-4 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Bulan</label>
                    <select name="month" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm px-3 py-2">
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ sprintf('%02d', $m) }}" {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->locale('id')->isoFormat('MMMM') }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Tahun</label>
                    <select name="year" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm px-3 py-2">
                        @for ($y = now()->year; $y >= 2024; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Karyawan</label>
                    <select name="employee_id" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm px-3 py-2">
                        <option value="">Semua</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Tampilkan</button>
                @if ($attendances->count() > 0)
                    <a href="{{ route('company.reports.attendance.pdf', request()->all()) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition" target="_blank">
                        📄 Download PDF
                    </a>
                @endif
            </form>
        </div>

        {{-- Report Table --}}
        @forelse ($grouped as $empId => $rows)
            @php $emp = $rows->first()->employee; @endphp
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-300">
                            {{ substr($emp->name, 0, 1) }}
                        </div>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $emp->name }}</span>
                        <span class="text-xs text-gray-400">({{ $emp->payment_type_label }})</span>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        Total: <strong class="text-gray-900 dark:text-gray-100">{{ $rows->count() }} hari</strong>
                        @php $totalHours = $rows->sum('work_hours'); @endphp
                        @if ($totalHours > 0)
                            · {{ number_format($totalHours, 1) }} jam
                        @endif
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Clock In</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Clock Out</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Jam Kerja</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($rows as $a)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <td class="px-4 py-2 text-gray-900 dark:text-gray-100">{{ $a->clock_in->format('d M Y') }}</td>
                                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-gray-100">{{ $a->clock_in->format('H:i') }}</td>
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $a->clock_out?->format('H:i') ?? '-' }}</td>
                                    <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $a->work_hours ? number_format($a->work_hours, 1) . ' jam' : '-' }}</td>
                                    <td class="px-4 py-2">
                                        @if ($a->is_manual_entry)
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300">Manual</span>
                                        @else
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300">Foto</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center text-gray-400 dark:text-gray-500">
                    <p class="font-medium">Belum ada data absensi</p>
                    <p class="text-sm mt-1">Tidak ada catatan absensi untuk periode ini.</p>
                </div>
            </div>
        @endforelse
    </div>
</x-app-layout>
