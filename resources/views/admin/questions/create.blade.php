<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Pertanyaan Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="bg-white p-6 rounded-xl">
                    <form method="POST" action="{{ route('admin.alat-tes.questions.store', ['alat_te' => $alatTeId]) }}">
                        @csrf

                        <input type="hidden" name="alat_tes_id" value="{{ $alatTeId }}">

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Pertanyaan</label>
                            <select id="type" name="type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="PILIHAN_GANDA" {{ old('type') == 'PILIHAN_GANDA' ? 'selected' : '' }}>Pilihan Ganda</option>
                                <option value="ESSAY" {{ old('type') == 'ESSAY' ? 'selected' : '' }}>Esai (Hanya Teks)</option>
                                <option value="HAFALAN" {{ old('type') == 'HAFALAN' ? 'selected' : '' }}>Hafalan (Materi Memori)</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="question-text-container" class="mb-6">
                            <label for="question_text" class="block text-sm font-medium text-gray-700">Teks Pertanyaan</label>
                            <textarea id="question_text" name="question_text" rows="4" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Masukkan teks pertanyaan di sini.">{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="options-container" class="border border-gray-200 p-4 rounded-lg space-y-4 hidden">
                            <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Opsi Jawaban</h3>

                            @php
                                $options = ['A', 'B', 'C', 'D']; // Default 4 Opsi
                            @endphp

                            @foreach ($options as $key => $label)
                            <div class="flex items-start space-x-3 bg-gray-50 p-3 rounded-lg">
                                
                                <div class="flex items-center pt-2">
                                    <input id="is_correct_{{ $key }}" type="radio" name="is_correct" value="{{ $key }}" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                    <label for="is_correct_{{ $key }}" class="ml-2 text-sm text-gray-600">Benar</label>
                                </div>
                                
                                <div class="flex-1">
                                    <label for="option_{{ $key }}" class="block text-xs font-medium text-gray-500">Opsi {{ $label }}</label>
                                    <input id="option_{{ $key }}" type="text" name="options[{{ $key }}][text]" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Masukkan teks untuk Opsi {{ $label }}">
                                    <input type="hidden" name="options[{{ $key }}][index]" value="{{ $key }}">
                                </div>
                            </div>
                            @endforeach

                            @error('options')
                                <p class="text-red-500 text-xs mt-1">Harap isi semua opsi jawaban dan tentukan satu jawaban yang benar.</p>
                            @enderror
                            @error('is_correct')
                                <p class="text-red-500 text-xs mt-1">Harap pilih salah satu opsi sebagai jawaban yang benar.</p>
                            @enderror
                        </div>
                        <div id="memory-container" class="border border-indigo-200 p-4 rounded-lg space-y-4 hidden">
                            <h3 class="text-lg font-semibold text-indigo-700 border-b pb-2">Materi Hafalan</h3>
                            
                            <div class="mb-4">
                                <label for="memory_content" class="block text-sm font-medium text-gray-700">Konten Memori (Teks/URL Gambar)</label>
                                <textarea id="memory_content" name="memory_content" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm" placeholder="Masukkan teks atau URL gambar yang harus dihafal.">{{ old('memory_content') }}</textarea>
                                @error('memory_content')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="memory_type" class="block text-sm font-medium text-gray-700">Tipe Konten</label>
                                <select id="memory_type" name="memory_type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                    <option value="TEXT">Teks</option>
                                    <option value="IMAGE">Gambar (Harap masukkan URL/path)</option>
                                </select>
                            </div>
                            
                            <div class="mb-4">
                                <label for="duration_seconds" class="block text-sm font-medium text-gray-700">Durasi Tampil (Detik)</label>
                                <input id="duration_seconds" name="duration_seconds" type="number" min="1" value="{{ old('duration_seconds', 10) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                                @error('duration_seconds')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <p class="text-sm text-indigo-600 pt-2">Setelah item memori ini dibuat, Anda akan menambahkan *soal recall* (Pilihan Ganda) yang merujuk padanya.</p>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md">
                                Simpan Pertanyaan
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const typeSelect = document.getElementById('type');
            const optionsContainer = document.getElementById('options-container');
            const memoryContainer = document.getElementById('memory-container');
            const questionTextContainer = document.getElementById('question-text-container');
            const questionTextarea = document.getElementById('question_text');
            
            function toggleContainers() {
                const selectedType = typeSelect.value;
                
                // Reset visibility
                optionsContainer.classList.add('hidden');
                memoryContainer.classList.add('hidden');
                questionTextContainer.classList.add('hidden');
                questionTextarea.required = false; // Non-required by default

                if (selectedType === 'PILIHAN_GANDA' || selectedType === 'ESSAY') {
                    // Tampilkan Teks Pertanyaan
                    questionTextContainer.classList.remove('hidden');
                    questionTextarea.required = true;
                }
                
                if (selectedType === 'PILIHAN_GANDA') {
                    // Tampilkan Opsi Jawaban
                    optionsContainer.classList.remove('hidden');
                } else if (selectedType === 'HAFALAN') {
                    // Tampilkan Kontainer Memori
                    memoryContainer.classList.remove('hidden');
                }
            }

            // Panggil saat halaman dimuat (untuk nilai default atau old value)
            toggleContainers();

            // Panggil saat nilai dropdown berubah
            typeSelect.addEventListener('change', toggleContainers);
        });
    </script>
</x-admin-layout>