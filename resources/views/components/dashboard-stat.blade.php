@props(['label', 'value', 'color' => 'indigo', 'icon' => 'people'])

@php
$colors = [
    'indigo' => 'bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300',
    'red' => 'bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300',
    'green' => 'bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300',
    'yellow' => 'bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300',
];
$iconColor = $colors[$color] ?? $colors['indigo'];

$icons = [
    'people' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>',
    'money' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>',
    'wallet' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>',
    'clipboard' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>',
];
@endphp

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-5">
        <div class="flex items-center gap-4">
            <div class="p-3 rounded-full {{ $iconColor }} shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icons[$icon] ?? $icons['people'] !!}
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">{{ $label }}</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $value }}</p>
            </div>
        </div>
    </div>
</div>
