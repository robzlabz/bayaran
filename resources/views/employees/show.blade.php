<x-app-layout>
    <x-slot name="header">{{ $employee->name }}</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        {{-- Profile Card --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 sm:gap-6">
                    <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center shrink-0">
                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-300">{{ substr($employee->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ $employee->name }}</h3>
                        <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1 text-sm text-gray-500 dark:text-gray-400">
                            <span>📱 {{ $employee->phone }}</span>
                            <span>💳 {{ $employee->payment_type_label }}</span>
                            <span>💰 {{ $employee->rate_label }}</span>
                            <span>📅 Gajian: tgl <strong>{{ $employee->pay_date ?? '-' }}</strong></span>
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Saldo</p>
                        <p class="text-2xl font-bold {{ $employee->balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            Rp {{ number_format($employee->balance, 0, ',', '.') }}
                        </p>
                        <a href="{{ route('company.employees.edit', $employee) }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline mt-1 inline-block">Edit</a>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Terakhir Absen</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1">
                            {{ $lastAttendance ? $lastAttendance->clock_in->format('d M Y') : '-' }}
                        </p>
                        @if ($lastAttendance)
                            <p class="text-xs text-gray-400">{{ $lastAttendance->clock_in->format('H:i') }} - {{ $lastAttendance->clock_out?->format('H:i') ?? '-' }}</p>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Terakhir Gajian</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1">
                            {{ $lastPayment ? $lastPayment->payment_date->format('d M Y') : '-' }}
                        </p>
                        @if ($lastPayment)
                            <p class="text-xs text-gray-400">Rp {{ number_format($lastPayment->total_amount, 0, ',', '.') }}</p>
                        @endif
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Total Hutang</p>
                        <p class="text-sm font-medium {{ $totalDebtRemaining > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }} mt-1">
                            Rp {{ number_format($totalDebtRemaining, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Sisa Cuti</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mt-1">
                            {{ $employee->leave_remaining }}/{{ $employee->leave_quota }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'attendance' }" class="space-y-4">
            {{-- Tab buttons --}}
            <div class="flex gap-1 bg-gray-100 dark:bg-gray-700/50 p-1 rounded-xl overflow-x-auto">
                <button @@click="tab = 'attendance'" :class="tab === 'attendance' ? 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-600/50'" class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition">📋 Absensi</button>
                <button @@click="tab = 'debts'" :class="tab === 'debts' ? 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-600/50'" class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition">💰 Hutang</button>
                <button @@click="tab = 'payments'" :class="tab === 'payments' ? 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-600/50'" class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition">💳 Pembayaran</button>
                <button @@click="tab = 'balance'" :class="tab === 'balance' ? 'bg-white dark:bg-gray-600 shadow-sm text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white hover:bg-gray-200 dark:hover:bg-gray-600/50'" class="px-4 py-2 text-sm font-medium rounded-lg whitespace-nowrap transition">🏦 Mutasi Saldo</button>
            </div>

            {{-- Tab: Absensi --}}
            <div x-show="tab === 'attendance'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Riwayat Absensi</h4>
                        <a href="{{ route('company.attendances.create', ['employee_id' => $employee->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded-lg transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Input Manual
                        </a>
                    </div>
                    @if ($attendances->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Clock In</th>
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Clock Out</th>
                                        <th class="py-2 text-right font-medium text-gray-500 dark:text-gray-400">Jam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attendances as $a)
                                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                            <td class="py-2 pr-4 text-gray-900 dark:text-gray-100">{{ $a->clock_in->format('d M Y') }}</td>
                                            <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">{{ $a->clock_in->format('H:i') }}</td>
                                            <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">{{ $a->clock_out?->format('H:i') ?? '-' }}</td>
                                            <td class="py-2 text-right text-gray-600 dark:text-gray-400">{{ $a->work_hours ? number_format($a->work_hours, 1) . 'h' : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-8">Belum ada riwayat absensi</p>
                    @endif
                </div>
            </div>

            {{-- Tab: Hutang --}}
            <div x-show="tab === 'debts'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" style="display: none;">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Riwayat Hutang</h4>
                        <div class="flex items-center gap-2">
                            @if ($totalDebtRemaining > 0)
                                <span class="text-xs text-red-600 dark:text-red-400 font-medium">Sisa: Rp {{ number_format($totalDebtRemaining, 0, ',', '.') }}</span>
                            @endif
                            <a href="{{ route('company.debts.create', ['employee_id' => $employee->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Catat Hutang
                            </a>
                        </div>
                    </div>
                    @if ($debts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Jumlah</th>
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Dibayar</th>
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Sisa</th>
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                                        <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($debts as $d)
                                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                            <td class="py-2 pr-4 text-gray-900 dark:text-gray-100">{{ $d->debt_date->format('d M Y') }}</td>
                                            <td class="py-2 pr-4 text-gray-900 dark:text-gray-100">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                                            <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">Rp {{ number_format($d->paid_amount, 0, ',', '.') }}</td>
                                            <td class="py-2 pr-4 {{ $d->remaining > 0 ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-500' }}">
                                                @if ($d->remaining > 0) Rp {{ number_format($d->remaining, 0, ',', '.') }} @else - @endif
                                            </td>
                                            <td class="py-2 pr-4">
                                                @if ($d->is_paid)
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">Lunas</span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">Sisa Rp {{ number_format($d->remaining, 0, ',', '.') }}</span>
                                                @endif
                                            </td>
                                            <td class="py-2 text-gray-500 dark:text-gray-400 max-w-[150px] truncate">{{ $d->description ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-8">Tidak ada hutang</p>
                    @endif
                </div>
            </div>

            {{-- Tab: Pembayaran --}}
            <div x-show="tab === 'payments'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" style="display: none;">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Riwayat Pembayaran Gaji</h4>
                        <a href="{{ route('company.payments.create', ['employee_id' => $employee->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Proses Pembayaran
                        </a>
                    </div>
                    @if ($payments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                                        <th class="py-2 pr-4 text-right font-medium text-gray-500 dark:text-gray-400">Total</th>
                                        <th class="py-2 pr-4 text-right font-medium text-gray-500 dark:text-gray-400">Gaji</th>
                                        <th class="py-2 pr-4 text-right font-medium text-gray-500 dark:text-gray-400">Bayar Hutang</th>
                                        <th class="py-2 text-left font-medium text-gray-500 dark:text-gray-400">Hutang Dibayar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $p)
                                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                            <td class="py-2 pr-4 text-gray-900 dark:text-gray-100">{{ $p->payment_date->format('d M Y') }}</td>
                                            <td class="py-2 pr-4 text-right font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($p->total_amount, 0, ',', '.') }}</td>
                                            <td class="py-2 pr-4 text-right text-green-600 dark:text-green-400">Rp {{ number_format($p->salary_amount, 0, ',', '.') }}</td>
                                            <td class="py-2 pr-4 text-right text-red-600 dark:text-red-400">Rp {{ number_format($p->debt_amount, 0, ',', '.') }}</td>
                                            <td class="py-2 text-gray-500 dark:text-gray-400">
                                                @if ($p->debts->count() > 0)
                                                    @foreach ($p->debts as $d)
                                                        <div class="text-xs">• {{ $d->description ?? 'Hutang' }}: Rp {{ number_format($d->pivot->amount, 0, ',', '.') }}</div>
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-8">Belum ada pembayaran</p>
                    @endif
                </div>
            </div>

            {{-- Tab: Mutasi Saldo --}}
            <div x-show="tab === 'balance'" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" style="display: none;">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mutasi Saldo</h4>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('company.transactions.topup', ['employee_id' => $employee->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Top Up
                            </a>
                            <a href="{{ route('company.transports.create', ['employee_id' => $employee->id]) }}" class="inline-flex items-center gap-1 px-3 py-1.5 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded-lg transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                Ongkos
                            </a>
                        </div>
                    </div>
                    @if ($transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                                        <th class="py-2 pr-4 text-left font-medium text-gray-500 dark:text-gray-400">Keterangan</th>
                                        <th class="py-2 pr-4 text-right font-medium text-gray-500 dark:text-gray-400">Jumlah</th>
                                        <th class="py-2 text-right font-medium text-gray-500 dark:text-gray-400">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($transactions as $tx)
                                        <tr class="border-b border-gray-100 dark:border-gray-700/50">
                                            <td class="py-2 pr-4 whitespace-nowrap text-gray-600 dark:text-gray-400">{{ $tx->transaction_date->format('d M Y') }}</td>
                                            <td class="py-2 pr-4">
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                                    @if($tx->type === 'topup') bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300
                                                    @elseif($tx->type === 'transport') bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-300
                                                    @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 @endif">
                                                    {{ $tx->type_label }}
                                                </span>
                                            </td>
                                            <td class="py-2 pr-4 text-gray-500 dark:text-gray-400 max-w-[150px] truncate">{{ $tx->description ?? '-' }}</td>
                                            <td class="py-2 pr-4 text-right font-medium {{ $tx->amount >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                                {{ $tx->amount >= 0 ? '+' : '' }}Rp {{ number_format(abs($tx->amount), 0, ',', '.') }}
                                            </td>
                                            <td class="py-2 text-right text-gray-600 dark:text-gray-400">Rp {{ number_format($tx->balance_after, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-8">Belum ada mutasi saldo</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
