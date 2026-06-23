<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Account Type -->
        <div class="mt-4">
            <x-input-label :value="__('Tipe Akun')" />
            <div class="mt-2 grid grid-cols-2 gap-3">
                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-4 shadow-sm focus:outline-none has-[:checked]:border-indigo-500 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500">
                    <input type="radio" name="account_type" value="personal"
                           class="sr-only" aria-labelledby="account-type-personal"
                           {{ old('account_type', 'personal') === 'personal' ? 'checked' : '' }}>
                    <div class="flex w-full items-center justify-between">
                        <div class="flex flex-col">
                            <span id="account-type-personal" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Personal</span>
                            <span class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">Gratis — untuk penggunaan sendiri</span>
                        </div>
                        <svg class="h-5 w-5 text-indigo-600 {{ old('account_type', 'personal') === 'personal' ? '' : 'invisible' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-check-icon>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </label>
                <label class="relative flex cursor-pointer rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 p-4 shadow-sm focus:outline-none has-[:checked]:border-indigo-500 has-[:checked]:ring-1 has-[:checked]:ring-indigo-500">
                    <input type="radio" name="account_type" value="company"
                           class="sr-only" aria-labelledby="account-type-company"
                           {{ old('account_type') === 'company' ? 'checked' : '' }}>
                    <div class="flex w-full items-center justify-between">
                        <div class="flex flex-col">
                            <span id="account-type-company" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Company</span>
                            <span class="mt-1 flex items-center text-xs text-gray-500 dark:text-gray-400">Rp5.000/karyawan — transfer bank</span>
                        </div>
                        <svg class="h-5 w-5 text-indigo-600 {{ old('account_type') === 'company' ? '' : 'invisible' }}" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" data-check-icon>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </label>
            </div>
            <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
        </div>

        <!-- Company Name (hidden by default, shown when company selected) -->
        <div id="company-name-field" class="mt-4 {{ old('account_type') === 'company' ? '' : 'hidden' }}">
            <x-input-label for="company_name" :value="__('Nama Perusahaan')" />
            <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" autocomplete="organization" />
            <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    @push('scripts')
    <script>
        document.querySelectorAll('input[name="account_type"]').forEach(el => {
            el.addEventListener('change', function() {
                // Toggle check icon visibility
                document.querySelectorAll('input[name="account_type"]').forEach(input => {
                    const icon = input.closest('label').querySelector('[data-check-icon]');
                    if (input.checked) {
                        icon.classList.remove('invisible');
                    } else {
                        icon.classList.add('invisible');
                    }
                });

                // Toggle company name field
                const companyField = document.getElementById('company-name-field');
                const companyInput = document.getElementById('company_name');
                if (this.value === 'company') {
                    companyField.classList.remove('hidden');
                    companyInput.setAttribute('required', '');
                } else {
                    companyField.classList.add('hidden');
                    companyInput.removeAttribute('required');
                }
            });
        });
    </script>
    @endpush
</x-guest-layout>
