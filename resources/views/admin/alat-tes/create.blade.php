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

                        {{-- ✅ SECTION: Contoh Soal dengan Dropdown --}}
                        <div class="mt-6 border-t pt-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                Contoh Soal <span class="text-gray-500 text-sm font-normal">(Opsional - Maksimal 2 contoh)</span>
                            </h3>

                            {{-- Contoh Soal 1 --}}
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-700">📝 Contoh Soal 1</h4>
                                    <select id="example_type_1" 
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Jenis Soal --</option>
                                        <option value="papi">PAPI (Personality)</option>
                                        <option value="pauli">Pauli Test (Numerical)</option>
                                        <option value="rmib">RMIB (Interest)</option>
                                        <option value="binary">Pilihan Ganda (2 Jawaban)</option>
                                        <option value="custom">Custom (Buat Sendiri)</option>
                                    </select>
                                </div>

                                {{-- Template PAPI --}}
                                <div id="template_papi_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question" 
                                            value="Saya lebih suka bekerja dalam tim daripada sendiri"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_1_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">Sangat Tidak Setuju
Tidak Setuju
Netral
Setuju
Sangat Setuju</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index)</label>
                                            <input type="number" name="example_1_correct" value="3" min="0" max="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation" value="Menunjukkan preferensi kerja tim" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template Pauli --}}
                                <div id="template_pauli_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question" 
                                            value="15 + 23 = ?"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_1_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">35
36
38
39
40</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index)</label>
                                            <input type="number" name="example_1_correct" value="2" min="0" max="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation" value="15 + 23 = 38" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template RMIB --}}
                                <div id="template_rmib_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question" 
                                            value="Pekerjaan yang paling Anda minati:"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_1_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">Programmer
Desainer Grafis
Guru
Dokter
Pengusaha</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index)</label>
                                            <input type="number" name="example_1_correct" value="0" min="0" max="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation" value="Minat di bidang teknologi" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template Binary (2 Jawaban) --}}
                                <div id="template_binary_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question" 
                                            value="Apakah Anda lebih suka bekerja di pagi hari?"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_1_options" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">Ya
Tidak</textarea>
                                        <p class="mt-1 text-xs text-gray-500">✅ Format 2 jawaban: Ya/Tidak, Benar/Salah, Setuju/Tidak Setuju</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index: 0-1)</label>
                                            <input type="number" name="example_1_correct" value="0" min="0" max="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <p class="mt-1 text-xs text-gray-500">0 = Ya, 1 = Tidak</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation" value="Menunjukkan preferensi waktu kerja optimal" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template Custom --}}
                                <div id="template_custom_1" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_1_question" 
                                            value="{{ old('example_1_question') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                            placeholder="Masukkan pertanyaan Anda...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban (pisahkan dengan enter)</label>
                                        <textarea name="example_1_options" rows="4"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                            placeholder="Pilihan A&#10;Pilihan B&#10;Pilihan C&#10;Pilihan D&#10;Pilihan E">{{ old('example_1_options') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Setiap baris = 1 pilihan jawaban</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index: 0-4)</label>
                                            <input type="number" name="example_1_correct" 
                                                value="{{ old('example_1_correct', 0) }}"
                                                min="0" max="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                                placeholder="0">
                                            <p class="mt-1 text-xs text-gray-500">0 = Pilihan pertama</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_1_explanation" 
                                                value="{{ old('example_1_explanation') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                                placeholder="Penjelasan jawaban...">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Contoh Soal 2 --}}
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-semibold text-gray-700">📝 Contoh Soal 2</h4>
                                    <select id="example_type_2" 
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Jenis Soal --</option>
                                        <option value="papi">PAPI (Personality)</option>
                                        <option value="pauli">Pauli Test (Numerical)</option>
                                        <option value="rmib">RMIB (Interest)</option>
                                        <option value="binary">Pilihan Ganda (2 Jawaban)</option>
                                        <option value="custom">Custom (Buat Sendiri)</option>
                                    </select>
                                </div>

                                {{-- Template PAPI --}}
                                <div id="template_papi_2" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_2_question" 
                                            value="Saya mudah beradaptasi dengan perubahan"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_2_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">Sangat Tidak Setuju
Tidak Setuju
Netral
Setuju
Sangat Setuju</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index)</label>
                                            <input type="number" name="example_2_correct" value="4" min="0" max="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_2_explanation" value="Menunjukkan fleksibilitas tinggi" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template Pauli --}}
                                <div id="template_pauli_2" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_2_question" 
                                            value="42 - 17 = ?"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_2_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">23
24
25
26
27</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index)</label>
                                            <input type="number" name="example_2_correct" value="2" min="0" max="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_2_explanation" value="42 - 17 = 25" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template RMIB --}}
                                <div id="template_rmib_2" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_2_question" 
                                            value="Aktivitas yang paling Anda sukai:"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_2_options" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">Membaca buku
Berolahraga
Menggambar
Bermain musik
Memasak</textarea>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index)</label>
                                            <input type="number" name="example_2_correct" value="0" min="0" max="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_2_explanation" value="Minat pada pembelajaran" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template Binary (2 Jawaban) --}}
                                <div id="template_binary_2" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_2_question" 
                                            value="Apakah Anda lebih suka bekerja sendiri daripada dalam tim?"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban</label>
                                        <textarea name="example_2_options" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100">Setuju
Tidak Setuju</textarea>
                                        <p class="mt-1 text-xs text-gray-500">✅ Format 2 jawaban: Ya/Tidak, Benar/Salah, Setuju/Tidak Setuju</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index: 0-1)</label>
                                            <input type="number" name="example_2_correct" value="1" min="0" max="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <p class="mt-1 text-xs text-gray-500">0 = Setuju, 1 = Tidak Setuju</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_2_explanation" value="Menunjukkan preferensi kerja kolaboratif" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        </div>
                                    </div>
                                </div>

                                {{-- Template Custom --}}
                                <div id="template_custom_2" class="template-content hidden">
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pertanyaan</label>
                                        <input type="text" name="example_2_question" 
                                            value="{{ old('example_2_question') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                            placeholder="Masukkan pertanyaan Anda...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm text-gray-600 mb-1">Pilihan Jawaban (pisahkan dengan enter)</label>
                                        <textarea name="example_2_options" rows="4"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                            placeholder="Pilihan A&#10;Pilihan B&#10;Pilihan C&#10;Pilihan D&#10;Pilihan E">{{ old('example_2_options') }}</textarea>
                                        <p class="mt-1 text-xs text-gray-500">Setiap baris = 1 pilihan jawaban</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Jawaban Benar (Index: 0-4)</label>
                                            <input type="number" name="example_2_correct" 
                                                value="{{ old('example_2_correct', 0) }}"
                                                min="0" max="4"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                                placeholder="0">
                                            <p class="mt-1 text-xs text-gray-500">0 = Pilihan pertama</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm text-gray-600 mb-1">Penjelasan</label>
                                            <input type="text" name="example_2_explanation" 
                                                value="{{ old('example_2_explanation') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                                placeholder="Penjelasan jawaban...">
                                        </div>
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

    {{-- JavaScript untuk handle dropdown --}}
    <script>
        // Handle dropdown untuk Contoh Soal 1
        document.getElementById('example_type_1').addEventListener('change', function() {
            const selectedType = this.value;
            const templates = document.querySelectorAll('#template_papi_1, #template_pauli_1, #template_rmib_1, #template_binary_1, #template_custom_1');
            
            templates.forEach(template => template.classList.add('hidden'));
            
            if (selectedType) {
                document.getElementById('template_' + selectedType + '_1').classList.remove('hidden');
            }
        });

        // Handle dropdown untuk Contoh Soal 2
        document.getElementById('example_type_2').addEventListener('change', function() {
            const selectedType = this.value;
            const templates = document.querySelectorAll('#template_papi_2, #template_pauli_2, #template_rmib_2, #template_binary_2, #template_custom_2');
            
            templates.forEach(template => template.classList.add('hidden'));
            
            if (selectedType) {
                document.getElementById('template_' + selectedType + '_2').classList.remove('hidden');
            }
        });
    </script>
</x-admin-layout>