<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-10"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
                
                {{-- Tombol Mata (Toggle Password) --}}
                <button type="button" onclick="togglePasswordLogin()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-600 hover:text-gray-900 focus:outline-none">
                    {{-- Icon Mata Coret (Hidden) --}}
                    <svg id="icon-hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                    {{-- Icon Mata Terbuka (Show) --}}
                    <svg id="icon-show" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </button>
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="captcha" value="Keamanan: Ketik kode di bawah" />

            <div class="flex items-center gap-2 mt-1">
                {{-- 1. Gambar Captcha --}}
                <div style="flex-shrink: 0; width: 100px;"> 
                    <img class="captcha-img rounded shadow-sm cursor-pointer w-full" 
                         src="{{ captcha_src('mini') }}" 
                         alt="captcha" 
                         onclick="refreshCaptcha()"
                         onerror="this.onerror=null; this.src='{{ captcha_src('mini') }}?' + Math.random();"
                         title="Klik gambar untuk refresh kode"
                         style="height: 42px; border: 1px solid #d1d5db; object-fit: cover;">
                </div>

                {{-- 2. Input Jawaban --}}
                <div style="flex-grow: 1;">
                    <x-text-input id="captcha" class="block w-full" type="text" name="captcha" required placeholder="Kode" style="height: 42px;" />
                </div>
            </div>
            
            <div class="text-right mt-1">
                <a href="#" onclick="refreshCaptcha(); return false;" class="text-xs text-indigo-600 hover:text-indigo-900">
                    Refresh Kode
                </a>
            </div>

            <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    {{-- SCRIPTS --}}
    <script>
        function refreshCaptcha() {
            document.querySelector('.captcha-img').src = '{{ captcha_src('mini') }}?' + Math.random();
            document.getElementById('captcha').value = '';
        }

        function togglePasswordLogin() {
            const passwordInput = document.getElementById('password');
            const iconHidden = document.getElementById('icon-hidden');
            const iconShow = document.getElementById('icon-show');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                iconHidden.classList.add('hidden');
                iconShow.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                iconHidden.classList.remove('hidden');
                iconShow.classList.add('hidden');
            }
        }
    </script>
</x-guest-layout>