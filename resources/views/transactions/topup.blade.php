<x-app-layout>
    <x-slot name="header">Top Up Saldo</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-300">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('company.transactions.topup.store') }}" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Karyawan</label>
                    <select id="employee_id" name="employee_id" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5">
                        <option value="">— Pilih karyawan —</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old("employee_id", $selectedEmployeeId) == $emp->id ? "selected" : "" }}>
                                {{ $emp->name }} — Rp {{ number_format($emp->balance, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Top Up</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">Rp</span>
                        <input type="number" id="amount" name="amount" value="{{ old('amount') }}" required min="1"
                               class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5"
                               placeholder="0">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input type="text" id="description" name="description" value="{{ old('description') }}"
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5"
                           placeholder="Misal: Top up saldo awal">
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <a href="{{ route('company.transactions.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                        Top Up
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
