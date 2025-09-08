<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Satu Langkah Lagi</h2>
        <p class="text-gray-600 mt-2">Kode tes Anda valid. Silakan masukkan nama lengkap Anda untuk memulai.</p>
    </div>

    <!-- Menampilkan pesan error jika ada -->
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

    <form method="POST" action="{{ route('test-code.start') }}">
        @csrf

        <!-- Nama Peserta -->
        <div>
            <x-input-label for="participant_name" value="Nama Lengkap" />
            <x-text-input id="participant_name" class="block mt-1 w-full" type="text" name="participant_name" :value="old('participant_name')" required autofocus />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Mulai Tes') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

