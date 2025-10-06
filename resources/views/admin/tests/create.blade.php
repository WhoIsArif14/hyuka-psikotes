<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Modul') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="flex justify-start items-center mb-6">
                    <a href="{{ route('admin.tests.index') }}"
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-150 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                        role="alert">
                        <strong class="font-bold">Oops!</strong>
                        <span class="block sm:inline"> Ada beberapa kesalahan pada input Anda.</span>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.tests.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2 mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700">Nama Modul:</label>
                            <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Nama modul">
                            <p class="mt-1 text-xs text-gray-500">NOTE: Modul test yang dibuat melalui dashboard ini
                                hanya menampilkan psikogram default</p>
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2 border border-gray-300 p-4 rounded-lg mb-6">
                            <h3 class="block text-sm font-medium text-gray-700 mb-2">Data Diri:</h3>
                            <p class="text-xs text-gray-500 mb-3">Silakan pilih salah satu:</p>

                            @foreach ($dataTypes as $value => $label)
                                <div class="flex items-center mb-2">
                                    <input id="data_type_{{ $value }}" name="required_data_type" type="radio"
                                        value="{{ $value }}"
                                        class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500"
                                        {{ old('required_data_type', 'DATA_DIRI') == $value ? 'checked' : '' }}>
                                    <label for="data_type_{{ $value }}"
                                        class="ml-3 block text-sm font-medium text-gray-900 uppercase">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach

                            @error('required_data_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="client_id" class="block text-sm font-medium text-gray-700">Klien Tes
                                (Opsional)</label>
                            <select id="client_id" name="client_id"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="">-- Pilih Klien --</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="test_category_id" class="block text-sm font-medium text-gray-700">Kategori
                                Tes</label>
                            <select id="test_category_id" name="test_category_id" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('test_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="jenjang_id" class="block text-sm font-medium text-gray-700">Jenjang</label>
                            <select id="jenjang_id" name="jenjang_id" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="">-- Pilih Jenjang --</option>
                                @foreach ($jenjangs as $jenjang)
                                    <option value="{{ $jenjang->id }}"
                                        {{ old('jenjang_id') == $jenjang->id ? 'selected' : '' }}>{{ $jenjang->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Durasi Total
                                (dalam Menit)</label>
                            <input type="number" id="duration_minutes" name="duration_minutes"
                                value="{{ old('duration_minutes', 0) }}" required readonly
                                class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm cursor-not-allowed">
                            <p class="mt-1 text-xs text-gray-500">Durasi dihitung otomatis berdasarkan Alat Tes yang
                                dipilih.</p>
                            @error('duration_minutes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="test_code" class="block text-sm font-medium text-gray-700">Kode Tes
                                (Opsional)</label>
                            <input type="text" id="test_code" name="test_code" value="{{ old('test_code') }}"
                                maxlength="8" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                            <p class="mt-1 text-xs text-gray-500">Kosongkan untuk membuat kode otomatis.</p>
                        </div>

                        <div class="md:col-span-2 border border-gray-300 p-4 rounded-lg">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Alat Tes (Pilih 1 atau
                                Lebih):</label>
                            <div class="grid grid-cols-2 gap-x-4 gap-y-2 max-h-60 overflow-y-auto p-2 border rounded-md"
                                id="alat_tes_list_container">
                                @foreach ($alatTes as $alatTes)
                                    @php
                                        $isChecked = in_array($alatTes->id, old('alat_tes_ids', []));
                                    @endphp
                                    <div class="flex items-center">
                                        <input id="alat_tes_{{ $alatTes->id }}" name="alat_tes_ids[]" type="checkbox"
                                            value="{{ $alatTes->id }}"
                                            data-duration="{{ $alatTes->duration_minutes }}"
                                            class="alat-tes-checkbox h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                            {{ $isChecked ? 'checked' : '' }}>
                                        <label for="alat_tes_{{ $alatTes->id }}"
                                            class="ml-2 block text-sm text-gray-900">
                                            {{ $alatTes->name }} ({{ $alatTes->duration_minutes }} menit)
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-3 text-sm font-bold text-gray-700">Akumulasi Waktu Tes: <span
                                    id="total_duration_display">{{ old('duration_minutes', 0) }}</span> menit</p>
                            @error('alat_tes_ids')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label for="description"
                                class="block text-sm font-medium text-gray-700">Deskripsi/Instruksi</label>
                            <textarea id="description" name="description" rows="3" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label for="available_from" class="block text-sm font-medium text-gray-700">Tersedia Mulai
                                (Opsional)</label>
                            <input type="datetime-local" id="available_from" name="available_from"
                                value="{{ old('available_from') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label for="available_to" class="block text-sm font-medium text-gray-700">Tersedia Hingga
                                (Opsional)</label>
                            <input type="datetime-local" id="available_to" name="available_to"
                                value="{{ old('available_to') }}"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                        </div>

                        <div class="md:col-span-2 flex items-center space-x-6">
                            <div class="flex items-center">
                                <input id="is_published" name="is_published" type="checkbox"
                                    {{ old('is_published') ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="is_published" class="ml-2 block text-sm text-gray-900">Langsung
                                    Publikasikan Tes</label>
                            </div>
                            <div class="flex items-center">
                                <input id="is_template" name="is_template" type="checkbox"
                                    {{ old('is_template') ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <label for="is_template" class="ml-2 block text-sm text-gray-900">Jadikan sebagai
                                    Template/Master</label>
                            </div>
                        </div>

                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <button type="button" onclick="window.history.back()"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            Batal
                        </button>
                        <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                            Simpan Modul
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Target semua checkbox Alat Tes
            const checkboxes = document.querySelectorAll('.alat-tes-checkbox');
            const durationInput = document.getElementById('duration_minutes');
            const durationDisplay = document.getElementById('total_duration_display');

            function updateDuration() {
                let totalDuration = 0;

                // Iterasi melalui semua checkbox
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const duration = parseInt(checkbox.getAttribute('data-duration'));
                        if (!isNaN(duration)) {
                            totalDuration += duration;
                        }
                    }
                });

                // Perbarui nilai input tersembunyi (untuk POST)
                durationInput.value = totalDuration;

                // Perbarui tampilan di bawah checklist
                durationDisplay.textContent = totalDuration;
            }

            // Pasang event listener ke setiap checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateDuration);
            });

            // Jalankan sekali saat halaman dimuat
            updateDuration();
        });
    </script>
</x-admin-layout>
