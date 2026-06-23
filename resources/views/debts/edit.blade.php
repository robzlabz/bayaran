<x-app-layout>
    <x-slot name="header">Edit Hutang</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-300">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('company.debts.update', $debt) }}" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Karyawan</label>
                    <select id="employee_id" name="employee_id" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                        <option value="">— Pilih karyawan —</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id', $debt->employee_id) == $emp->id ? 'selected' : '' }}>
                                {{ $emp->name }} ({{ $emp->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Hutang</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" id="amount" name="amount" value="{{ old('amount', $debt->amount) }}" required min="1"
                               class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                    </div>
                </div>

                <div>
                    <label for="debt_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Hutang</label>
                    <input type="date" id="debt_date" name="debt_date" value="{{ old('debt_date', $debt->debt_date->format('Y-m-d')) }}" required
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                    <input type="text" id="description" name="description" value="{{ old('description', $debt->description) }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">{{ old('notes', $debt->notes) }}</textarea>
                </div>

                {{-- Status --}}
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_paid" name="is_paid" value="1"
                           {{ old('is_paid', $debt->is_paid) ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600 text-green-600 shadow-sm focus:ring-green-500">
                    <label for="is_paid" class="text-sm text-gray-700 dark:text-gray-300">Sudah lunas</label>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <a href="{{ route('company.debts.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
