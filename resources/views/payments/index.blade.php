<x-app-layout>
    <x-slot name="header">Riwayat Pembayaran</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-300">{!! session('success') !!}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Dibayarkan</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Porsi Gaji</p>
                <p class="text-2xl font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($totalSalary, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Porsi Hutang</p>
                <p class="text-2xl font-semibold text-red-600 dark:text-red-400">Rp {{ number_format($totalDebtPaid, 0, ',', '.') }}</p>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <a href="{{ route('company.payments.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Proses Pembayaran
            </a>
            <form method="GET">
                <select name="employee_id" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm px-3 py-2">
                    <option value="">Semua karyawan</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
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
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Total</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Gaji</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Bayar Hutang</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Hutang Dibayar</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Catatan</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($payments as $p)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $p->payment_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-300 shrink-0">
                                            {{ substr($p->employee->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $p->employee->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-green-600 dark:text-green-400">Rp {{ number_format($p->salary_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right text-red-600 dark:text-red-400">Rp {{ number_format($p->debt_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-[150px]">
                                    @if ($p->debts->count() > 0)
                                        @foreach ($p->debts as $d)
                                            <div class="text-xs">• {{ $d->description ?? 'Hutang' }}: Rp {{ number_format($d->pivot->amount, 0, ',', '.') }}</div>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-400 max-w-[120px] truncate">{{ $p->notes ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('company.payments.destroy', $p) }}" onsubmit="return confirm('Hapus riwayat pembayaran ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="font-medium">Belum ada pembayaran</p>
                                    <p class="mt-1 text-sm">Proses pembayaran gaji pertama Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $payments->links() }}</div>
    </div>
</x-app-layout>
