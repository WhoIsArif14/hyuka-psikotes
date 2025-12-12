<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah Alat Tes Baru
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    {{-- Error Messages --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <strong class="font-bold">Gagal Menyimpan!</strong>
                            <span class="block sm:inline">Silakan periksa input Anda dan coba lagi.</span>
                            <ul class="mt-2">
                                @foreach ($errors->all() as $error)
                                    <li class="text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.alat-tes.store') }}">
                        @csrf

                        {{-- Nama Alat Tes --}}
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">
                                Nama Alat Tes <span class="text-red-500">*</span>
                            </label>
                            <input id="name"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                type="text" name="name" value="{{ old('name') }}" required autofocus />
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Durasi --}}
                        <div class="mt-4">
                            <label for="duration_minutes" class="block font-medium text-sm text-gray-700">
                                Durasi (dalam Menit) <span class="text-red-500">*</span>
                            </label>
                            <input id="duration_minutes"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('duration_minutes') border-red-500 @enderror"
                                type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" required />
                            @error('duration_minutes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Instruksi Tes --}}
                        <div class="mt-4">
                            <label for="instructions" class="block font-medium text-sm text-gray-700">
                                Petunjuk Soal <span class="text-gray-500">(Opsional)</span>
                            </label>
                            <textarea id="instructions" name="instructions" rows="5"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('instructions') border-red-500 @enderror"
                                placeholder="Masukkan petunjuk pengerjaan soal...">{{ old('instructions') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Petunjuk ini akan ditampilkan kepada peserta sebelum memulai tes.
                            </p>
                            @error('instructions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ✅ SECTION: Contoh Soal --}}
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                Contoh Soal <span class="text-gray-500 text-sm font-normal">(Opsional - Maksimal 2 contoh)</span>
                            </h3>

                            {{-- Contoh Soal 1 --}}
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <h4 class="font-semibold text-gray-700 mb-3">📝 Contoh Soal 1</h4>
                                
                                <div class="mb-3">
                                    <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                    <input type="text" name="example_1_question" 
                                        value="{{ old('example_1_question') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Contoh: Apa ibu kota Indonesia?">
                                </div>

                                <div class="mb-3">
                                    <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban (pisahkan dengan enter)</label>
                                    <textarea name="example_1_options" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Jakarta&#10;Bandung&#10;Surabaya&#10;Medan&#10;Bali">{{ old('example_1_options') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Setiap baris = 1 pilihan jawaban</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index: 0-4)</label>
                                        <input type="number" name="example_1_correct" 
                                            value="{{ old('example_1_correct', 0) }}"
                                            min="0" max="4"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="0">
                                        <p class="mt-1 text-xs text-gray-500">0 = Pilihan pertama</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                        <input type="text" name="example_1_explanation" 
                                            value="{{ old('example_1_explanation') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="Penjelasan jawaban...">
                                    </div>
                                </div>
                            </div>

                            {{-- Contoh Soal 2 --}}
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-700 mb-3">📝 Contoh Soal 2</h4>
                                
                                <div class="mb-3">
                                    <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                    <input type="text" name="example_2_question" 
                                        value="{{ old('example_2_question') }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="Contoh: 1 + 1 = ?">
                                </div>

                                <div class="mb-3">
                                    <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban (pisahkan dengan enter)</label>
                                    <textarea name="example_2_options" rows="4"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="1&#10;2&#10;3&#10;4&#10;5">{{ old('example_2_options') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Setiap baris = 1 pilihan jawaban</p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index: 0-4)</label>
                                        <input type="number" name="example_2_correct" 
                                            value="{{ old('example_2_correct', 0) }}"
                                            min="0" max="4"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="1">
                                        <p class="mt-1 text-xs text-gray-500">0 = Pilihan pertama</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                        <input type="text" name="example_2_explanation" 
                                            value="{{ old('example_2_explanation') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="Penjelasan jawaban...">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="flex items-center justify-end mt-6 pt-6 border-t">
                            <a href="{{ route('admin.alat-tes.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4">
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Simpan Alat Tes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>