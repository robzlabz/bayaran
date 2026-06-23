<x-app-layout>
    <x-slot name="header">Izin & Cuti</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-300">{!! session('success') !!}</div>
        @endif

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Izin</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $totalLeaves }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Sakit</p>
                <p class="text-2xl font-semibold text-red-600 dark:text-red-400">{{ $sickCount }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Izin</p>
                <p class="text-2xl font-semibold text-yellow-600 dark:text-yellow-400">{{ $permissionCount }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-5">
                <p class="text-sm text-gray-500 dark:text-gray-400">Cuti</p>
                <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400">{{ $annualCount }}</p>
            </div>
        </div>

        {{-- Filter & Actions --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <a href="{{ route('company.leaves.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Catat Izin
            </a>

            <form method="GET" class="flex items-center gap-2">
                <select name="employee_id" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm px-3 py-2">
                    <option value="">Semua karyawan</option>
                    @foreach ($employees as $emp)
                        <option value="{{ $emp->id }}" {{ $employeeId == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->leave_remaining }}/{{ $emp->leave_quota }})
                        </option>
                    @endforeach
                </select>
                <select name="year" onchange="this.form.submit()"
                        class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 text-sm px-3 py-2">
                    @for ($y = now()->year; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Karyawan</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Dari</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Sampai</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Hari</th>
                            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Alasan</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($leaves as $l)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-xs font-bold text-blue-600 dark:text-blue-300 shrink-0">
                                            {{ substr($l->employee->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $l->employee->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($l->type === 'sick') bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                                        @elseif($l->type === 'permission') bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300
                                        @else bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 @endif">
                                        {{ $l->type_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $l->start_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $l->end_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $l->days }} hari</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 max-w-[150px] truncate">{{ $l->reason ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <form method="POST" action="{{ route('company.leaves.destroy', $l) }}" onsubmit="return confirm('Hapus izin ini?')" class="inline">
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
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="font-medium">Belum ada izin</p>
                                    <p class="mt-1 text-sm">Catat izin atau cuti karyawan di sini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">{{ $leaves->links() }}</div>
    </div>
</x-app-layout>
