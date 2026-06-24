<x-app-layout>
    <x-slot name="header">Input Absensi Manual</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-300">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('company.attendances.store') }}" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Karyawan</label>
                    <select id="employee_id" name="employee_id" required
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm px-4 py-2.5">
                        <option value="">— Pilih karyawan —</option>
                        @foreach ($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old("employee_id", $selectedEmployeeId) == $emp->id ? "selected" : "" }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="clock_in" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jam Masuk</label>
                        <input type="datetime-local" id="clock_in" name="clock_in" value="{{ old('clock_in', now()->format('Y-m-d\TH:i')) }}" required
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm px-4 py-2.5">
                    </div>
                    <div>
                        <label for="clock_out" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jam Pulang <span class="text-gray-400 font-normal">(opsional)</span></label>
                        <input type="datetime-local" id="clock_out" name="clock_out" value="{{ old('clock_out') }}"
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm px-4 py-2.5">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                    <textarea id="notes" name="notes" rows="2"
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-yellow-500 focus:ring-yellow-500 text-sm px-4 py-2.5"
                              placeholder="Alasan input manual (misal: lupa clock in)">{{ old('notes') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <a href="{{ route('company.attendances.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition">
                        Simpan Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
