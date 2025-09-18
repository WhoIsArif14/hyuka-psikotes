<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola Soal untuk: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Form Tambah Soal & Pilihan Jawaban Baru -->
            <div x-data="{ questionType: 'multiple_choice' }" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Tambah Soal Baru</h3>
                    
                    <form action="{{ route('admin.tests.questions.store', $test) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Menampilkan Error Validasi -->
                        <x-auth-validation-errors class="mb-4" :errors="$errors" />

                        <!-- Tipe Soal -->
                        <div class="mb-4">
                            <label for="type" class="block font-medium text-sm text-gray-700">Tipe Soal</label>
                            <select name="type" id="type" x-model="questionType" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                <option value="multiple_choice">Pilihan Ganda (Teks/Gambar)</option>
                                <option value="image_upload">Upload Jawaban Gambar</option>
                            </select>
                        </div>
                        
                        <!-- Pertanyaan (Umum untuk semua tipe) -->
                        <div class="mb-4">
                            <label for="question_text" class="block font-medium text-sm text-gray-700">Teks Pertanyaan</label>
                            <textarea name="question_text" id="question_text" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>{{ old('question_text') }}</textarea>
                        </div>
                        <div class="mb-6">
                            <label for="question_image" class="block font-medium text-sm text-gray-700">Gambar untuk Pertanyaan (Opsional)</label>
                            <input type="file" name="question_image" id="question_image" class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                        </div>


                        <!-- Bagian Khusus Pilihan Ganda -->
                        <div x-show="questionType === 'multiple_choice'" class="border-t pt-4">
                            <h4 class="text-md font-medium text-gray-700 mb-2">Pilihan Jawaban</h4>
                            <div class="space-y-4">
                                @for ($i = 0; $i < 6; $i++)
                                    <div class="flex items-center gap-3 p-3 border rounded-md">
                                        <input type="radio" name="correct_option" value="{{ $i }}" class="h-5 w-5 text-indigo-600 border-gray-300 focus:ring-indigo-500" {{ $i == 0 ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <label class="sr-only">Teks Opsi {{ chr(65 + $i) }}</label>
                                            <input type="text" name="options[]" placeholder="Teks Opsi {{ chr(65 + $i) }}" class="block w-full rounded-md shadow-sm border-gray-300">
                                        </div>
                                        <div class="flex-1">
                                            <label class="sr-only">Gambar Opsi {{ chr(65 + $i) }}</label>
                                            <input type="file" name="option_images[]" class="block w-full text-sm text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100"/>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                            <small class="text-gray-500 mt-2 block">Pilih radio button untuk menandai jawaban benar. Kosongkan teks atau gambar jika tidak diperlukan.</small>
                        </div>

                        <!-- Bagian Khusus Upload Jawaban -->
                        <div x-show="questionType === 'image_upload'">
                            <p class="text-sm text-gray-600 bg-yellow-50 border border-yellow-200 p-3 rounded-md">
                                Untuk tipe soal ini, peserta akan diberikan tombol untuk mengunggah jawaban mereka dalam bentuk gambar. Tidak ada pilihan jawaban yang perlu diisi.
                            </p>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Daftar Soal yang Ada -->
            <h3 class="text-2xl font-semibold mb-4 text-gray-700">Daftar Soal</h3>
            @forelse ($questions as $question)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-6 text-gray-900 border-b">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="text-lg font-semibold">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                                <span class="text-xs font-semibold uppercase rounded-full px-2 py-1 {{ $question->type === 'multiple_choice' ? 'bg-indigo-100 text-indigo-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ str_replace('_', ' ', $question->type) }}
                                </span>
                                @if($question->image_path)
                                    <img src="{{ Storage::disk('public')->url($question->image_path) }}" alt="Gambar Soal" class="mt-2 rounded-md max-w-sm">
                                @endif
                            </div>
                            <div class="flex space-x-2 flex-shrink-0 ml-4">
                                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    @if($question->type === 'multiple_choice')
                        <div class="p-6 text-gray-900">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($question->options as $option)
                                    <div class="flex items-start p-2 rounded {{ $option->point > 0 ? 'bg-green-100' : 'bg-gray-50' }}">
                                        @if($option->point > 0)
                                            <svg class="w-5 h-5 text-green-600 mr-2 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-400 mr-2 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        @endif
                                        <div class="flex-1">
                                            <p class="{{ $option->point > 0 ? 'font-bold text-green-800' : 'text-gray-800' }}">{{ $option->option_text }}</p>
                                            @if($option->image_path)
                                                <img src="{{ Storage::disk('public')->url($option->image_path) }}" alt="Gambar Opsi" class="mt-2 rounded-md max-w-xs">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 text-center">
                        <p>Belum ada soal untuk tes ini.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</x-admin-layout>