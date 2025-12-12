<x-guest-layout>
    <div class="w-full sm:max-w-xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Lengkapi Data Diri Anda</h2>
            <p class="text-gray-600 mt-2">Data ini diperlukan sebelum Anda dapat memulai tes.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4">
                <div class="font-medium text-red-600">Terjadi kesalahan saat menyimpan data:</div>
                <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form method="POST" action="{{ route('user.data.update') }}">
            @csrf

            <div class="mt-4">
                <x-input-label for="name" value="Nama Lengkap" />
                <x-text-input 
                    id="name" 
                    class="block mt-1 w-full" 
                    type="text" 
                    name="name" 
                    value="{{ old('name', $user->name === ('Peserta ' . $user->kode_aktivasi_peserta) ? '' : $user->name) }}" 
                    required 
                    autofocus 
                    autocomplete="name"
                />
            </div>

            <div class="mt-4">
                <x-input-label for="email" value="Email (Wajib Aktif)" />
                <x-text-input 
                    id="email" 
                    class="block mt-1 w-full" 
                    type="email" 
                    name="email" 
                    value="{{ old('email', Str::endsWith($user->email, '@temp.hyuka.com') ? '' : $user->email) }}"
                    required 
                    autocomplete="email"
                />
            </div>

            <div class="mt-4">
                <x-input-label for="phone_number" value="Nomor Telepon (WhatsApp)" />
                <x-text-input 
                    id="phone_number" 
                    class="block mt-1 w-full" 
                    type="text" 
                    name="phone_number" 
                    value="{{ old('phone_number', $user->phone_number) }}"
                />
            </div>

            <div class="mt-4">
                <x-input-label for="education" value="Pendidikan Terakhir" />
                <x-text-input 
                    id="education" 
                    class="block mt-1 w-full" 
                    type="text" 
                    name="education" 
                    value="{{ old('education', $user->education) }}"
                />
            </div>

            <div class="mt-4">
                <x-input-label for="major" value="Jurusan/Fakultas" />
                <x-text-input 
                    id="major" 
                    class="block mt-1 w-full" 
                    type="text" 
                    name="major" 
                    value="{{ old('major', $user->major) }}"
                />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button class="w-full justify-center">
                    {{ __('KIRIM') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>