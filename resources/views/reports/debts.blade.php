<x-app-layout>
    <x-slot name="header">Rekap Hutang</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        {{-- Summary --}}
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Hutang</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalDebt, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum Lunas</p>
                <p class="text-2xl font-semibold text-red-600 dark:text-red-400">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
            </div>
        </div>

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
                <div>
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Status</label>
                    <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm px-3 py-2">
                        <option value="">Semua</option>
                        <option value="unpaid" {{ $status === 'unpaid' ? 'selected' : '' }}>Belum Lunas</option>
                        <option value="paid" {{ $status === 'paid' ? 'selected' : '' }}>Lunas</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">Tampilkan</button>
                @if ($debts->count() > 0)
                    <a href="{{ route('company.reports.debts.pdf', request()->all()) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition" target="_blank">
                        📄 Download PDF
                    </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Karyawan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Jumlah</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Keterangan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($debts as $d)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $d->debt_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">{{ substr($d->employee->name, 0, 1) }}</div>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $d->employee->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ $d->description ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($d->is_paid)
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Lunas</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">Belum</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">Belum ada data hutang untuk periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
