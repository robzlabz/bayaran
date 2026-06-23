<x-app-layout>
    <x-slot name="header">Absensi</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="max-w-lg mx-auto">
            <!-- Clock Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-center">
                <div class="p-8">
                    <!-- Time Display -->
                    <div class="mb-6">
                        <p class="text-5xl font-bold text-gray-900 dark:text-gray-100" id="clock-display">--:--:--</p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" id="date-display">--</p>
                    </div>

                    <!-- Employee Info -->
                    <div class="mb-6">
                        <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mx-auto">
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-300">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                        </div>
                        <p class="mt-3 text-lg font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Karyawan</p>
                    </div>

                    <!-- Clock In/Out Button -->
                    <button id="clock-btn" class="w-full py-4 px-6 rounded-xl text-lg font-bold text-white transition-all duration-200 bg-green-600 hover:bg-green-700 active:scale-95 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                        Clock In
                    </button>

                    <!-- Status -->
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status hari ini</p>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300" id="clock-status">Belum clock in</p>
                    </div>

                    <!-- Last Attendance -->
                    <div class="mt-6 text-left">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Riwayat Terakhir</h4>
                        <div class="space-y-2" id="attendance-history">
                            <p class="text-sm text-gray-400 dark:text-gray-500 text-center">Belum ada riwayat absensi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Clock display
        function updateClock() {
            const now = new Date();
            const time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            const date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            document.getElementById('clock-display').textContent = time;
            document.getElementById('date-display').textContent = date;
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
    @endpush
</x-app-layout>
