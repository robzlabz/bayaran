<x-app-layout>
    <x-slot name="header">Absensi</x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="max-w-lg mx-auto">
            {{-- Clock Card --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-center" x-data="attendanceApp()" x-init="init()">
                <div class="p-8">
                    {{-- Time --}}
                    <div class="mb-6">
                        <p class="text-5xl font-bold text-gray-900 dark:text-gray-100" id="clock-display" x-text="time">--:--:--</p>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400" id="date-display" x-text="date">--</p>
                    </div>

                    {{-- Employee --}}
                    <div class="mb-6">
                        <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center mx-auto">
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-300">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <p class="mt-3 text-lg font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->phone }}</p>
                    </div>

                    {{-- Camera Preview --}}
                    <div x-show="showCamera" class="mb-4" style="display: none;">
                        <video id="camera-preview" autoplay playsinline class="w-full rounded-xl bg-black"></video>
                        <canvas id="camera-canvas" style="display:none;"></canvas>
                    </div>

                    {{-- Photo Result --}}
                    <div x-show="photoTaken" class="mb-4" style="display: none;">
                        <img id="photo-result" class="w-full rounded-xl" src="" alt="Foto">
                    </div>

                    {{-- Clock Button --}}
                    <button x-show="!showCamera && !photoTaken"
                            @@click="clockAction()"
                            id="clock-btn"
                            class="w-full py-4 px-6 rounded-xl text-lg font-bold text-white transition-all duration-200 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                            :class="isClockedIn ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500'"
                            x-text="isClockedIn ? 'Clock Out' : 'Clock In'">
                        Clock In
                    </button>

                    {{-- Capture Button --}}
                    <button x-show="showCamera"
                            @@click="capturePhoto()"
                            class="w-full py-4 px-6 rounded-xl text-lg font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all">
                        📸 Ambil Foto
                    </button>

                    {{-- Confirm Button --}}
                    <button x-show="photoTaken && !submitting"
                            @@click="submitAttendance()"
                            class="w-full py-4 px-6 rounded-xl text-lg font-bold text-white bg-green-600 hover:bg-green-700 transition-all">
                        ✅ Konfirmasi & <span x-text="isClockedIn ? 'Clock Out' : 'Clock In'"></span>
                    </button>

                    {{-- Loading --}}
                    <div x-show="submitting" class="w-full py-4 px-6 rounded-xl text-lg font-bold text-white bg-gray-400 text-center">
                        <svg class="animate-spin h-6 w-6 mx-auto" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                    </div>

                    {{-- Status --}}
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status hari ini</p>
                        <p class="text-sm font-medium" id="clock-status" x-text="statusText">Memuat...</p>
                    </div>

                    {{-- Result Message --}}
                    <div x-show="resultMessage" class="mt-4 p-4 rounded-lg" :class="resultSuccess ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300'" x-text="resultMessage" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function attendanceApp() {
            return {
                time: '',
                date: '',
                isClockedIn: false,
                statusText: 'Memuat...',
                showCamera: false,
                photoTaken: false,
                submitting: false,
                resultMessage: '',
                resultSuccess: false,
                action: 'clock_in',

                init() {
                    this.updateClock();
                    setInterval(() => this.updateClock(), 1000);
                    this.checkStatus();
                },

                updateClock() {
                    const now = new Date();
                    this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    this.date = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                },

                async checkStatus() {
                    try {
                        const resp = await fetch('/employee/attendance/status');
                        const data = await resp.json();
                        this.isClockedIn = data.is_clocked_in;
                        if (data.is_clocked_in) {
                            this.statusText = 'Sedang clock in. Belum clock out.';
                        } else if (data.attendance && data.attendance.clock_out) {
                            this.statusText = 'Selesai. Clock out: ' + new Date(data.attendance.clock_out).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                        } else {
                            this.statusText = 'Belum clock in';
                        }
                        this.action = this.isClockedIn ? 'clock_out' : 'clock_in';
                    } catch(e) {
                        this.statusText = 'Gagal memuat status';
                    }
                },

                clockAction() {
                    this.showCamera = true;
                    this.photoTaken = false;
                    this.resultMessage = '';
                    setTimeout(() => this.startCamera(), 100);
                },

                async startCamera() {
                    try {
                        const video = document.getElementById('camera-preview');
                        const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment', width: 640, height: 480 } });
                        video.srcObject = stream;
                    } catch(e) {
                        // Camera not available - submit without photo
                        this.showCamera = false;
                        this.submitAttendance(null);
                    }
                },

                capturePhoto() {
                    const video = document.getElementById('camera-preview');
                    const canvas = document.getElementById('camera-canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);

                    // Stop camera
                    if (video.srcObject) {
                        video.srcObject.getTracks().forEach(t => t.stop());
                    }

                    // Show result
                    const img = document.getElementById('photo-result');
                    img.src = canvas.toDataURL('image/jpeg', 0.8);
                    this.photoTaken = true;
                    this.showCamera = false;
                },

                async submitAttendance() {
                    this.submitting = true;
                    this.resultMessage = '';

                    const canvas = document.getElementById('camera-canvas');

                    try {
                        // Convert canvas to blob
                        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.8));
                        const formData = new FormData();
                        if (blob) {
                            formData.append('photo', blob, 'attendance.jpg');
                        }

                        const endpoint = this.isClockedIn ? '/employee/attendance/clock-out' : '/employee/attendance/clock-in';
                        const resp = await fetch(endpoint, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await resp.json();

                        if (resp.ok) {
                            this.resultSuccess = true;
                            this.resultMessage = data.message || 'Berhasil!';
                            this.photoTaken = false;
                            this.showCamera = false;
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            this.resultSuccess = false;
                            this.resultMessage = data.error || 'Gagal';
                            this.photoTaken = false;
                            this.showCamera = false;
                        }
                    } catch(e) {
                        this.resultSuccess = false;
                        this.resultMessage = 'Gagal terhubung ke server';
                        this.photoTaken = false;
                        this.showCamera = false;
                    }

                    this.submitting = false;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
