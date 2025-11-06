<x-guest-layout>
    <!-- Session Status -->
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

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

      <div class="mt-4">
            {{-- Mengubah value label menjadi string biasa --}}
            <x-input-label for="captcha" value="Jawab: Masukkan kode di bawah" /> 

            <!-- Menampilkan gambar Captcha -->
            <div class="mt-1 flex items-center">
                {{-- MEMANGGIL HELPER LANGSUNG: Syntax ini 100% BENAR --}}
                <img class="captcha-img" src="{{ captcha_src('flat') }}" alt="captcha" style="border: 1px solid #ccc; border-radius: 4px;"> 
                
                {{-- Tombol Refresh --}}
                <button type="button" class="ms-2 text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline" 
                        onclick="document.querySelector('.captcha-img').src = '{{ captcha_src('flat') }}?' + new Date().getTime(); return false;">
                    Refresh
                </button>
            </div>
            
            <!-- Input Captcha -->
            <x-text-input id="captcha" class="block mt-2 w-full" type="text" name="captcha" required />

            <!-- Menampilkan error Captcha -->
            <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
        </div>


        <!-- Remember Me -->
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
</x-guest-layout>
