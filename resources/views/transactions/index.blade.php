<x-app-layout>
    <x-slot name="header">Mutasi Saldo</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-300">
                {!! session('success') !!}
            </div>
        @endif

        {{-- Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div class="flex items-center gap-2">
                <a href="{{ route('company.transactions.topup') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Top Up Saldo
                </a>
                <a href="{{ route('company.transports.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    Catat Ongkos
                </a>
            </div>

            {{-- Filter --}}
            <form method="GET" class="flex items-center gap-2">
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
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Keterangan</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Jumlah</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Saldo Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($transactions as $tx)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 whitespace-nowrap">{{ $tx->transaction_date->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300 shrink-0">
                                            {{ substr($tx->employee->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $tx->employee->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($tx->type === 'topup') bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300
                                        @elseif($tx->type === 'transport') bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300
                                        @elseif($tx->type === 'salary') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                        {{ $tx->type_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-[200px] truncate">{{ $tx->description ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-medium {{ $tx->amount >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $tx->amount >= 0 ? '+' : '' }}Rp {{ number_format(abs($tx->amount), 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-400">Rp {{ number_format($tx->balance_after, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <p class="font-medium">Belum ada transaksi</p>
                                    <p class="mt-1 text-sm">Top up saldo atau catat ongkos untuk memulai.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $transactions->links() }}</div>
    </div>
</x-app-layout>
