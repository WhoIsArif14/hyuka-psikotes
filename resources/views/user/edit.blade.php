<x-guest-layout>
    <div class="w-full sm:max-w-xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Lengkapi Data Diri Anda</h2>
            <p class="text-gray-600 mt-2">Data ini diperlukan sebelum Anda dapat memulai tes.</p>
        </div>

        @if(isset($currentModule) && $currentModule)
        <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-indigo-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-indigo-900">Modul Tes yang Diikuti</h3>
                    <p class="text-lg font-bold text-indigo-700 mt-1">{{ $currentModule->name }}</p>
                    @if($currentModule->description)
                    <p class="text-xs text-indigo-600 mt-1">{{ Str::limit($currentModule->description, 100) }}</p>
                    @endif
                    <p class="text-xs text-gray-500 mt-2">Kode Modul: <span class="font-mono bg-white px-2 py-0.5 rounded">{{ $currentModule->test_code }}</span></p>
                </div>
            </div>
        </div>
        @endif

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

        <form method="POST" action="{{ route('user.data.update') }}" id="userDataForm">
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
                    type="tel"
                    name="phone_number"
                    value="{{ old('phone_number', $user->phone_number) }}"
                    maxlength="13"
                    pattern="[0-9]*"
                    inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                    placeholder="Contoh: 08123456789"
                />
                <p class="mt-1 text-xs text-gray-500">Maksimal 13 digit angka</p>
            </div>

            <div class="mt-4">
                <x-input-label for="education" value="Pendidikan Terakhir" />
                <select
                    id="education"
                    name="education"
                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                >
                    <option value="">-- Pilih Jenjang Pendidikan --</option>
                    @foreach($jenjangs as $jenjang)
                        <option value="{{ $jenjang->name }}" {{ old('education', $user->education) == $jenjang->name ? 'selected' : '' }}>
                            {{ $jenjang->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4">
                <x-input-label for="major" value="Jurusan/Fakultas" />
                <x-text-input
                    id="major"
                    class="block mt-1 w-full"
                    type="text"
                    name="major"
                    value="{{ old('major', $user->major) }}"
                    placeholder="Contoh: Teknik Informatika"
                />
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button class="w-full justify-center">
                    {{ __('KIRIM') }}
                </x-primary-button>
            </div>
        </form>
    </div>

    <script>
        // Additional phone number validation
        document.getElementById('phone_number').addEventListener('input', function(e) {
            // Remove any non-numeric characters
            let value = e.target.value.replace(/[^0-9]/g, '');
            // Limit to 13 digits
            if (value.length > 13) {
                value = value.slice(0, 13);
            }
            e.target.value = value;
        });

        // Prevent paste of non-numeric characters
        document.getElementById('phone_number').addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numericText = pastedText.replace(/[^0-9]/g, '').slice(0, 13);
            this.value = numericText;
        });
    </script>
</x-guest-layout>