<x-app-layout>
    <x-slot name="header">Edit Karyawan</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-300">
                <p class="font-medium mb-1">Mohon perbaiki kesalahan berikut:</p>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('company.employees.update', $employee) }}" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Nama --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Karyawan</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $employee->name) }}" required
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                </div>

                {{-- No HP --}}
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}" required
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                </div>

                {{-- Tipe Pembayaran --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe Pembayaran</label>
                    <div class="grid grid-cols-4 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none has-[:checked]:border-indigo-500 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500">
                            <input type="radio" name="payment_type" value="monthly" class="sr-only"
                                   {{ old('payment_type', $employee->payment_type) === 'monthly' ? 'checked' : '' }}
                                   onchange="togglePaymentFields()">
                            <div class="text-center w-full">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Bulanan</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Gaji tetap</span>
                            </div>
                        </label>
                        <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none has-[:checked]:border-green-500 has-[:checked]:ring-1 has-[:checked]:ring-green-500">
                            <input type="radio" name="payment_type" value="daily" class="sr-only"
                                   {{ old('payment_type', $employee->payment_type) === 'daily' ? 'checked' : '' }}
                                   onchange="togglePaymentFields()">
                            <div class="text-center w-full">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Harian</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Upah per hari</span>
                            </div>
                        </label>
                        <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none has-[:checked]:border-purple-500 has-[:checked]:ring-1 has-[:checked]:ring-purple-500">
                            <input type="radio" name="payment_type" value="hourly" class="sr-only"
                                   {{ old("payment_type", $employee->payment_type) === "hourly" ? "checked" : "" }}
                                   onchange="togglePaymentFields()">
                            <div class="text-center w-full">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Per Jam</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Upah per jam</span>
                            </div>
                        </label>
                        <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 shadow-sm focus:outline-none has-[:checked]:border-yellow-500 has-[:checked]:ring-1 has-[:checked]:ring-yellow-500">
                            <input type="radio" name="payment_type" value="per_delivery" class="sr-only"
                                   {{ old('payment_type', $employee->payment_type) === 'per_delivery' ? 'checked' : '' }}
                                   onchange="togglePaymentFields()">
                            <div class="text-center w-full">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Pengantaran</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Per antar</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Nominal --}}
                <div id="field-monthly" class="payment-field {{ old('payment_type', $employee->payment_type) === 'monthly' ? '' : 'hidden' }}">
                    <label for="salary_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gaji Bulanan</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" id="salary_amount" name="salary_amount" value="{{ old('salary_amount', $employee->salary_amount) }}"
                               class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                    </div>
                </div>

                <div id="field-daily" class="payment-field {{ old('payment_type', $employee->payment_type) === 'daily' ? '' : 'hidden' }}">
                    <label for="daily_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upah Harian</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" id="daily_rate" name="daily_rate" value="{{ old('daily_rate', $employee->daily_rate) }}"
                               class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-green-500 text-sm px-4 py-2.5">
                    </div>
                </div>

                <div id="field-hourly" class="payment-field {{ old('payment_type', $employee->payment_type) === 'hourly' ? '' : 'hidden' }}">
                    <label for="hourly_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upah Per Jam</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" id="hourly_rate" name="hourly_rate" value="{{ old('hourly_rate', $employee->hourly_rate) }}"
                               class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-purple-500 focus:ring-purple-500 text-sm px-4 py-2.5">
                        <p class="mt-1 text-xs text-gray-400">Total gaji = jam kerja x upah per jam. Dihitung otomatis saat clock out.</p>
                    </div>
                </div>

                <div id="field-delivery" class="payment-field {{ old('payment_type', $employee->payment_type) === 'per_delivery' ? '' : 'hidden' }}">
                    <label for="delivery_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tarif Per Pengantaran</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" id="delivery_rate" name="delivery_rate" value="{{ old('delivery_rate', $employee->delivery_rate) }}"
                               class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm px-4 py-2.5">
                    </div>
                </div>

                {{-- Active Status --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                           {{ old('is_active', $employee->is_active) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Karyawan aktif</label>
                </div>

                {{-- Info Password --}}
                {{-- Jadwal Gajian --}}
                <div>
                    <label for="pay_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jadwal Gajian <span class="text-gray-400 font-normal">(tanggal setiap bulan)</span></label>
                    <input type="number" id="pay_date" name="pay_date" value="{{ old("'pay_date'", $employee->pay_date) }}" min="1" max="31"
                           class="block w-32 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5"
                           placeholder="25">
                </div>

                @if ($employee->user)
                    <div class="p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-xs text-gray-500 dark:text-gray-400">
                        Password login: <strong>{{ $employee->user->password_default ?? '(sudah diubah)' }}</strong>
                    </div>
                @endif

                {{-- Submit --}}
                <div class="flex items-center gap-3 pt-2">
                    <a href="{{ route('company.employees.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        Simpan Perubahan
                    </button>
                </div>

            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    function togglePaymentFields() {
        document.querySelectorAll('.payment-field').forEach(el => el.classList.add('hidden'));
        const val = document.querySelector('input[name="payment_type"]:checked')?.value;
        if (val) {
            document.getElementById('field-' + val)?.classList.remove('hidden');
        }
    }
    </script>
    @endpush
</x-app-layout>
