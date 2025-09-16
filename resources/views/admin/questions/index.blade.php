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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Tambah Soal Baru (dengan Gambar)</h3>
                    
                    <form action="{{ route('admin.tests.questions.store', $test) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Pertanyaan -->
                        <div class="mb-4">
                            <label for="question_text" class="block font-medium text-sm text-gray-700">Teks Pertanyaan</label>
                            <textarea name="question_text" id="question_text" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" required>{{ old('question_text') }}</textarea>
                        </div>
                        <div class="mb-6">
                            <label for="question_image" class="block font-medium text-sm text-gray-700">Gambar untuk Pertanyaan (Opsional)</label>
                            <input type="file" name="question_image" id="question_image" class="block w-full mt-1 text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"/>
                        </div>


                        <!-- Pilihan Jawaban -->
                        <h4 class="text-md font-medium text-gray-700 mb-2">Pilihan Jawaban</h4>
                        <div class="space-y-4">
                            @for ($i = 0; $i < 6; $i++)
                                <div class="flex items-center gap-3 p-3 border rounded-md">
                                    <input type="radio" name="correct_option" value="{{ $i }}" class="h-5 w-5 text-indigo-600 border-gray-300 focus:ring-indigo-500" required>
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
                                {{-- PERBAIKAN DI SINI --}}
                                @if($question->image_path)
                                    <img src="{{ asset('storage/' . $question->image_path) }}" alt="Gambar Soal" class="mt-2 rounded-md max-w-sm">
                                @endif
                            </div>
                            {{-- ... (tombol hapus tidak berubah) ... --}}
                        </div>
                    </div>
                    
                    <div class="p-6 text-gray-900">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($question->options as $option)
                                <div class="flex items-start p-2 rounded {{ $option->point > 0 ? 'bg-green-100' : 'bg-gray-50' }}">
                                    {{-- ... (icon benar/salah tidak berubah) ... --}}
                                    <div class="flex-1">
                                        <p class="{{ $option->point > 0 ? 'font-bold text-green-800' : 'text-gray-800' }}">{{ $option->option_text }}</p>
                                        {{-- PERBAIKAN DI SINI --}}
                                        @if($option->image_path)
                                            <img src="{{ asset('storage/' . $option->image_path) }}" alt="Gambar Opsi" class="mt-2 rounded-md max-w-xs">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            {{-- PERBAIKAN ADA DI BARIS DI BAWAH INI --}}
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