<x-guest-layout>
    <div class="mb-4 text-center">
        <a href="/">
            <x-hyuka-logo class="w-20 h-20 mx-auto text-indigo-500" />
        </a>
        <h2 class="mt-6 text-2xl font-bold text-gray-900">Satu Langkah Lagi</h2>
        <p class="mt-2 text-sm text-gray-600">
            Kode tes Anda valid. Silakan lengkapi data diri Anda untuk memulai.
        </p>
    </div>

    <!-- Menampilkan Error Validasi -->
    <x-auth-validation-errors class="mb-4" :errors="$errors" />

    <form method="POST" action="{{ route('test-code.start') }}">
        @csrf
        <div class="space-y-4">
            <!-- Nama Lengkap -->
            <div>
                <x-input-label for="participant_name" value="Nama Lengkap" />
                <x-text-input id="participant_name" class="block mt-1 w-full" type="text" name="participant_name" :value="old('participant_name')" required autofocus />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="participant_email" value="Alamat Email" />
                <x-text-input id="participant_email" class="block mt-1 w-full" type="email" name="participant_email" :value="old('participant_email')" required />
            </div>

            <!-- Nomor HP -->
            <div>
                <x-input-label for="phone_number" value="Nomor HP" />
                <x-text-input id="phone_number" class="block mt-1 w-full" type="tel" name="phone_number" :value="old('phone_number')" required />
            </div>

            <!-- Pendidikan & Jurusan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="education" value="Pendidikan Terakhir" />
                    <select id="education" name="education" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option>SMA/SMK</option>
                        <option>D3</option>
                        <option>S1</option>
                        <option>S2</option>
                        <option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="major" value="Jurusan" />
                    <x-text-input id="major" class="block mt-1 w-full" type="text" name="major" :value="old('major')" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Kirim') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>