<x-app-layout>
    <x-slot name="header">Proses Pembayaran</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-300">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" x-data="paymentApp()">
            <form method="POST" action="{{ route('company.payments.store') }}" class="p-6 space-y-5">
                @csrf

                {{-- Employee --}}
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Karyawan</label>
                    <select id="employee_id" name="employee_id" required @@change="selectEmployee($event.target.value)"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                        <option value="">— Pilih karyawan —</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" data-balance="{{ $emp->balance }}" data-salary="{{ $emp->salary_amount ?? $emp->daily_rate ?? $emp->hourly_rate ?? 0 }}" data-paydate="{{ $emp->pay_date }}" {{ old("employee_id", $selectedEmployeeId) == $emp->id ? "selected" : "" }}>
                                {{ $emp->name }}
                                @if ($emp->pay_date) — Tgl {{ $emp->pay_date }} @endif
                                @if ($emp->debts->count() > 0) — Hutang: Rp {{ number_format($emp->debts->sum('remaining'), 0, ',', '.') }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Info card --}}
                <div x-show="selectedEmployee" class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg space-y-2 text-sm" style="display: none;">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Saldo saat ini</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100" x-text="'Rp ' + formatMoney(balance)">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Gaji per periode</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100" x-text="'Rp ' + formatMoney(salaryAmount)">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Jadwal gajian</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100" x-text="payDate ? 'Tgl ' + payDate : 'Belum diatur'">Belum diatur</span>
                    </div>
                    <div x-show="totalDebt > 0" class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-gray-500 dark:text-gray-400">Total hutang</span>
                        <span class="font-medium text-red-600 dark:text-red-400" x-text="'Rp ' + formatMoney(totalDebt)">Rp 0</span>
                    </div>
                </div>

                {{-- Total Amount --}}
                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Dibayarkan</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" id="total_amount" name="total_amount" value="{{ old('total_amount') }}" required min="1" @@input="splitAmounts()"
                               class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5"
                               placeholder="0">
                    </div>
                </div>

                {{-- Split into salary and debt --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="salary_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Porsi Gaji
                            <span class="text-gray-400 font-normal">(ke saldo)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="number" id="salary_amount" name="salary_amount" value="{{ old('salary_amount', 0) }}" min="0" @@input="recalcFromSalary()"
                                   class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm px-4 py-2.5"
                                   placeholder="0">
                        </div>
                    </div>
                    <div>
                        <label for="debt_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Porsi Hutang
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                            <input type="number" id="debt_amount" name="debt_amount" value="{{ old('debt_amount', 0) }}" min="0" @@input="recalcFromDebt()"
                                   class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm px-4 py-2.5"
                                   placeholder="0">
                        </div>
                    </div>
                </div>

                {{-- Debt selection --}}
                <div x-show="totalDebt > 0" class="space-y-2" style="display: none;">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Pilih hutang yang dibayar</p>
                    <template x-for="debt in debts" :key="debt.id">
                        <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <input type="checkbox" :name="'debt_ids[]'" :value="debt.id" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="debt.description || 'Hutang'"></p>
                                <p class="text-xs text-gray-500" x-text="'Sisa: Rp ' + formatMoney(debt.remaining)"></p>
                            </div>
                            <span class="text-sm font-medium text-red-600 dark:text-red-400" x-text="'Rp ' + formatMoney(debt.remaining)"></span>
                        </label>
                    </template>
                </div>

                {{-- Date --}}
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Bayar</label>
                    <input type="date" id="payment_date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea id="notes" name="notes" rows="2"
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <a href="{{ route('company.payments.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function paymentApp() {
            return {
                selectedEmployee: false,
                balance: 0,
                salaryAmount: 0,
                payDate: '',
                totalDebt: 0,
                debts: [],

                selectEmployee(id) {
                    if (!id) { this.selectedEmployee = false; return; }
                    const opt = document.querySelector('#employee_id option[value="' + id + '"]');
                    if (!opt) return;
                    this.selectedEmployee = true;
                    this.balance = parseFloat(opt.dataset.balance) || 0;
                    this.salaryAmount = parseFloat(opt.dataset.salary) || 0;
                    this.payDate = opt.dataset.paydate || '';
                    this.fetchDebts(id);
                },

                async fetchDebts(id) {
                    try {
                        const resp = await fetch('/employee/attendance/status');
                        // For now, just set empty debts
                        this.debts = [];
                        this.totalDebt = 0;
                    } catch(e) {
                        this.debts = [];
                        this.totalDebt = 0;
                    }
                },

                splitAmounts() {
                    const total = parseFloat(document.querySelector('#total_amount').value) || 0;
                    // Auto-split: salary first, rest to debt
                    const salary = Math.min(total, this.salaryAmount || total);
                    document.querySelector('#salary_amount').value = salary;
                    document.querySelector('#debt_amount').value = total - salary;
                },

                recalcFromSalary() {
                    const total = parseFloat(document.querySelector('#total_amount').value) || 0;
                    const salary = parseFloat(document.querySelector('#salary_amount').value) || 0;
                    document.querySelector('#debt_amount').value = Math.max(0, total - salary);
                },

                recalcFromDebt() {
                    const total = parseFloat(document.querySelector('#total_amount').value) || 0;
                    const debt = parseFloat(document.querySelector('#debt_amount').value) || 0;
                    document.querySelector('#salary_amount').value = Math.max(0, total - debt);
                },

                formatMoney(val) {
                    return Number(val).toLocaleString('id-ID');
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
