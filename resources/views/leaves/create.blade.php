<x-app-layout>
    <x-slot name="header">Catat Izin / Cuti</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl mx-auto">

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-300">
                <ul class="list-disc list-inside">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <form method="POST" action="{{ route('company.leaves.store') }}" class="p-6 space-y-5">
                @csrf

                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Karyawan</label>
                    <x-searchable-select
                        name="employee_id"
                        label="Karyawan"
                        placeholder="— Pilih karyawan —"
                        :options="$employees->map(fn($e) => ['value' => $e->id, 'label' => $e->name . ' — ' . $e->leave_remaining . '/' . $e->leave_quota, 'subtext' => $e->phone ?? ''])->values()->toArray()"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 shadow-sm has-[:checked]:border-red-500 has-[:checked]:ring-1 has-[:checked]:ring-red-500">
                            <input type="radio" name="type" value="sick" class="sr-only" {{ old('type', 'permission') === 'sick' ? 'checked' : '' }}>
                            <div class="text-center w-full">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Sakit</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">🤒</span>
                            </div>
                        </label>
                        <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 shadow-sm has-[:checked]:border-yellow-500 has-[:checked]:ring-1 has-[:checked]:ring-yellow-500">
                            <input type="radio" name="type" value="permission" class="sr-only" {{ old('type', 'permission') === 'permission' ? 'checked' : '' }}>
                            <div class="text-center w-full">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Izin</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">📋</span>
                            </div>
                        </label>
                        <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 p-4 shadow-sm has-[:checked]:border-blue-500 has-[:checked]:ring-1 has-[:checked]:ring-blue-500">
                            <input type="radio" name="type" value="annual_leave" class="sr-only" {{ old('type') === 'annual_leave' ? 'checked' : '' }}>
                            <div class="text-center w-full">
                                <span class="block text-sm font-medium text-gray-900 dark:text-gray-100">Cuti</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">🏖️</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-4 py-2.5">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Selesai</label>
                        <input type="date" id="end_date" name="end_date" value="{{ old('end_date', now()->format('Y-m-d')) }}" required
                               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-4 py-2.5">
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alasan</label>
                    <textarea id="reason" name="reason" rows="3"
                              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm px-4 py-2.5"
                              placeholder="Jelaskan alasan izin / cuti">{{ old('reason') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <a href="{{ route('company.leaves.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                        Simpan Izin
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
