<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Selamat Datang di Hyuka</h2>
        {{-- Pesan disesuaikan untuk Kode Aktivasi Peserta --}}
        <p class="text-gray-600 mt-2">Silakan masukkan <b>Kode Aktivasi Peserta</b> Anda untuk melanjutkan.</p>
    </div>
    
    <!-- Menampilkan pesan error dari session (misalnya jika controller lama mengembalikannya) -->
    @if (session('error'))
        <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-3 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    {{-- Menampilkan error dari validasi field, terutama 'kode_aktivasi_peserta' --}}
    @if ($errors->any())
        <div class="mb-4">
            {{-- Hapus Whoops! dan tampilkan error spesifik --}}
            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM BARU: Menggunakan rute 'login' dan field 'kode_aktivasi_peserta' --}}
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Kode Aktivasi Peserta -->
        <div>
            {{-- Menggunakan X-Input-Label yang tersedia di layout Anda --}}
            <x-input-label for="kode_aktivasi_peserta" value="Kode Aktivasi Peserta" class="sr-only" />
            
            <x-text-input 
                id="kode_aktivasi_peserta" 
                class="block mt-1 w-full text-center font-mono uppercase" 
                type="text" 
                name="kode_aktivasi_peserta" 
                :value="old('kode_aktivasi_peserta')" 
                required 
                autofocus 
                placeholder="XXXX-XXXX" 
            />
            
            {{-- Menampilkan error khusus untuk field ini jika ada --}}
            <x-input-error :messages="$errors->get('kode_aktivasi_peserta')" class="mt-2" />
        </div>

        <div class="flex flex-col items-center justify-center mt-4">
            <x-primary-button class="w-full text-center flex justify-center">
                {{ __('MASUK DAN MULAI TES') }}
            </x-primary-button>

            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mt-4" href="{{ route('login.admin') }}">
                {{ __('Login sebagai Admin') }}
            </a>
        </div>
    </form>
</x-guest-layout>
