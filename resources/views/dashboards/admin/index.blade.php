<x-app-layout>
    <x-slot name="header">Admin Dashboard</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <h3 class="text-lg font-medium">Super Admin Panel</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola semua pengguna dan perusahaan.</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Owner</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Models\User::where('role', 'owner')->count() }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Karyawan</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Models\User::where('role', 'employee')->count() }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Perusahaan</p>
                    <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ \App\Models\User::where('account_type', 'company')->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mb-4">Semua Pengguna</h4>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-900">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Nama</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Email</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Role</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach (\App\Models\User::orderBy('created_at', 'desc')->get() as $u)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $u->name }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $u->email }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if($u->role === 'super_admin') bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300
                                        @elseif($u->role === 'owner') bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300
                                        @endif">
                                        {{ $u->role }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $u->account_type }}</td>
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $u->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
