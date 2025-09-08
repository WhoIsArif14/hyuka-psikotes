<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang di Hyuka</h2>
        <p class="text-gray-600 mt-2">Silakan masukkan kode tes unik Anda untuk memulai.</p>
    </div>
    
    <!-- Menampilkan pesan error jika ada -->
    @if (session('error'))
        <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-3 rounded-md">
            {{ session('error') }}
        </div>
    @endif
     @if ($errors->any())
        <div class="mb-4">
            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>

            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('test-code.process') }}">
        @csrf

        <!-- Kode Tes -->
        <div>
            <x-input-label for="test_code" value="Kode Tes" class="sr-only" />
            <x-text-input id="test_code" class="block mt-1 w-full text-center font-mono uppercase" type="text" name="test_code" :value="old('test_code')" required autofocus placeholder="XXXX-XXXX" />
        </div>

        <div class="flex flex-col items-center justify-center mt-4">
            <x-primary-button class="w-full text-center flex justify-center">
                {{ __('Lanjutkan') }}
            </x-primary-button>

            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-4" href="{{ route('login.admin') }}">
                {{ __('Login sebagai Admin') }}
            </a>
        </div>
    </form>
</x-guest-layout>

