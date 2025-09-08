<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Tes Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    
                    <form method="POST" action="{{ route('admin.tests.store') }}">
                        @csrf

                        <!-- Penampil Pesan Error Validasi -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <!-- Judul Tes -->
                        <div>
                            <label for="title" class="block font-medium text-sm text-gray-700">Judul Tes</label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="title" value="{{ old('title') }}" required autofocus />
                        </div>

                        <!-- Kategori, Jenjang, & Klien -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
                            <div>
                                <label for="test_category_id" class="block font-medium text-sm text-gray-700">Kategori Tes</label>
                                <select name="test_category_id" id="test_category_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('test_category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenjang_id" class="block font-medium text-sm text-gray-700">Jenjang</label>
                                <select name="jenjang_id" id="jenjang_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>
                                    <option value="">-- Pilih Jenjang --</option>
                                    @foreach($jenjangs as $jenjang)
                                        <option value="{{ $jenjang->id }}" @selected(old('jenjang_id') == $jenjang->id)>{{ $jenjang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="client_id" class="block font-medium text-sm text-gray-700">Klien (Opsional)</label>
                                <select name="client_id" id="client_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                    <option value="">-- Tes Umum --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" @selected(old('client_id') == $client->id)>{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi/Instruksi</label>
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" rows="4">{{ old('description') }}</textarea>
                        </div>
                        
                        <!-- Durasi & Kode Tes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label for="duration_minutes" class="block font-medium text-sm text-gray-700">Durasi (dalam Menit)</label>
                                <input id="duration_minutes" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" required />
                            </div>
                            <div>
                                <label for="test_code" class="block font-medium text-sm text-gray-700">Kode Tes (Opsional)</label>
                                <input id="test_code" class="block mt-1 w-full font-mono" type="text" name="test_code" value="{{ old('test_code') }}" />
                                <small class="text-gray-500">Kosongkan untuk generate kode otomatis.</small>
                            </div>
                        </div>

                        <!-- Jadwal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                             <div>
                                <label for="available_from" class="block font-medium text-sm text-gray-700">Tersedia Mulai (Opsional)</label>
                                <input id="available_from" class="block mt-1 w-full" type="datetime-local" name="available_from" value="{{ old('available_from') }}" />
                            </div>
                            <div>
                                <label for="available_to" class="block font-medium text-sm text-gray-700">Tersedia Hingga (Opsional)</label>
                                <input id="available_to" class="block mt-1 w-full" type="datetime-local" name="available_to" value="{{ old('available_to') }}" />
                            </div>
                        </div>

                        <!-- Opsi Checkbox -->
                        <div class="mt-4 space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm" name="is_published" value="1" @checked(old('is_published'))>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Langsung Publikasikan Tes') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm" name="is_template" value="1" @checked(old('is_template'))>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Jadikan sebagai Template/Master') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.tests.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
