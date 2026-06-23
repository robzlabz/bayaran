@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300'
            : 'flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
