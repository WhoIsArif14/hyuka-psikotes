<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Satu Langkah Lagi!</h2>
        <p class="text-gray-600">Kode Anda valid. Silakan isi data diri Anda untuk memulai tes.</p>
    </div>

    <form method="POST" action="{{ route('code.register.post') }}">
        @csrf

        <!-- Kode Aktivasi (tersembunyi) -->
        <input type="hidden" name="activation_code" value="{{ $code }}">

        <!-- Nama -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" value="Alamat Email" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Mulai Tes
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

