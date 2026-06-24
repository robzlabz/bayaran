@props(['name', 'options', 'selected' => null, 'placeholder' => '— Pilih —', 'label' => ''])

<div x-data="searchableSelect({
    name: '{{ $name }}',
    options: @js($options),
    selected: '{{ $selected ?? old($name) }}',
    placeholder: '{{ $placeholder }}'
})" class="relative">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ $label }}</label>
    @endif

    <input type="hidden" :name="name" x-model="selectedValue">

    {{-- Trigger --}}
    <button type="button" @@click="open = !open; if(open) $nextTick(() => $refs.search.focus())"
            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2.5 text-left truncate
                   {{ $attributes->get('class') }}"
            :class="!selectedLabel ? 'text-gray-400' : ''">
        <span x-text="selectedLabel || placeholder">— Pilih —</span>
        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>

    {{-- Dropdown --}}
    <div x-show="open" @@click.outside="open = false" class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-hidden flex flex-col"
         style="display: none;">
        {{-- Search --}}
        <div class="p-2 border-b border-gray-200 dark:border-gray-600">
            <input x-ref="search" type="text" x-model="search"
                   class="w-full px-3 py-1.5 text-sm rounded-md border border-gray-300 dark:border-gray-500 dark:bg-gray-600 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500"
                   placeholder="Ketik nama atau no HP...">
        </div>
        {{-- Options --}}
        <div class="overflow-y-auto flex-1">
            <template x-for="opt in filteredOptions" :key="opt.value">
                <button type="button" @@click="select(opt.value, opt.label); open = false"
                        class="w-full px-4 py-2.5 text-sm text-left hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition"
                        :class="selectedValue === opt.value ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-medium' : 'text-gray-700 dark:text-gray-300'">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-300 shrink-0" x-text="opt.label.charAt(0)"></div>
                        <div>
                            <div x-text="opt.label" class="font-medium"></div>
                            <div x-text="opt.subtext || ''" class="text-xs text-gray-400"></div>
                        </div>
                    </div>
                </button>
            </template>
            <div x-show="filteredOptions.length === 0" class="px-4 py-6 text-center text-sm text-gray-400">
                Tidak ditemukan
            </div>
        </div>
    </div>
</div>
