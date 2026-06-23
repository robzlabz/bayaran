<x-app-layout>
    <x-slot name="header">{{ $employee->name }}</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Info Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <div class="w-20 h-20 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl font-bold text-indigo-600 dark:text-indigo-300">{{ substr($employee->name, 0, 1) }}</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $employee->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $employee->phone }}</p>

                        <div class="mt-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                @if($employee->payment_type === 'monthly') bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                                @elseif($employee->payment_type === 'daily') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                                @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 @endif">
                                {{ $employee->payment_type_label }}
                            </span>
                        </div>

                        <div class="mt-4 text-sm">
                            <p class="text-gray-500 dark:text-gray-400">Nominal</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $employee->rate_label }}</p>
                        </div>

                        <div class="mt-2 text-sm">
                            <p class="text-gray-500 dark:text-gray-400">Saldo</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">Rp {{ number_format($employee->balance, 0, ',', '.') }}</p>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('company.employees.edit', $employee) }}" class="inline-flex items-center gap-1 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                Edit Karyawan
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats & History --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Ringkasan</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Hutang</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rp 0</p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                                <p class="text-xs text-gray-500 dark:text-gray-400">Saldo Saat Ini</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($employee->balance, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Info Login</h4>
                        @if ($employee->user)
                            <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                <p>No. HP: <strong>{{ $employee->user->phone }}</strong></p>
                                <p>Password default: <strong>{{ $employee->user->password_default ?? '(sudah diubah)' }}</strong></p>
                                <p class="text-xs text-gray-400 mt-2">Karyawan bisa login di <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">/employee/login</code></p>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Akun login belum tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
