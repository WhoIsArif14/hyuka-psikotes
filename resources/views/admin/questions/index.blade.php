<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Kelola Soal untuk: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Tambah Soal Baru</h3>
                    <form action="{{ route('admin.tests.questions.store', $test) }}" method="POST">
                        @csrf
                        <div>
                            <label for="question_text" class="block font-medium text-sm text-gray-700">Teks Soal</label>
                            <textarea name="question_text" id="question_text" rows="3" class="block mt-1 w-full rounded-md shadow-sm border-gray-300"></textarea>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Simpan Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <h3 class="text-2xl font-semibold mb-4 text-gray-700">Daftar Soal</h3>
            @forelse ($questions as $question)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                    <div class="p-6 text-gray-900 border-b">
                        <div class="flex justify-between items-start">
                            <p class="text-lg flex-1">{{ $loop->iteration }}. {{ $question->question_text }}</p>
                            <div class="flex space-x-2 flex-shrink-0 ml-4">
                                <a href="{{ route('admin.questions.edit', $question) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form method="POST" action="{{ route('admin.questions.destroy', $question) }}" onsubmit="return confirm('Yakin ingin menghapus soal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Pilihan Jawaban --}}
                    <div class="p-6 text-gray-900">
                        <h4 class="text-md font-medium text-gray-600 mb-2">Pilihan Jawaban:</h4>
                        <div class="space-y-2 mb-4">
                            @forelse($question->options as $option)
                            <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                <span>{{ $option->option_text }}</span>
                                <div class="flex items-center space-x-4">
                                    <span class="text-sm font-bold text-gray-600">Poin: {{ $option->point }}</span>
                                    <form method="POST" action="{{ route('admin.options.destroy', $option) }}" onsubmit="return confirm('Yakin ingin menghapus pilihan ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-xs">Hapus</button>
                                    </form>
                                </div>
                            </div>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada pilihan jawaban.</p>
                            @endforelse
                        </div>

                        {{-- Form Tambah Pilihan Jawaban Baru --}}
                        <form action="{{ route('admin.questions.options.store', $question) }}" method="POST" class="mt-4 border-t pt-4">
                            @csrf
                            <div class="flex space-x-4 items-end">
                                <div class="flex-1">
                                    <label for="option_text_{{ $question->id }}" class="block text-sm font-medium text-gray-700">Teks Pilihan Baru</label>
                                    <input type="text" name="option_text" id="option_text_{{ $question->id }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300" required>
                                </div>
                                <div class="w-1/4">
                                    <label for="point_{{ $question->id }}" class="block text-sm font-medium text-gray-700">Poin</label>
                                    <input type="number" name="point" id="point_{{ $question->id }}" class="mt-1 block w-full rounded-md shadow-sm border-gray-300" required value="0">
                                </div>
                                <div>
                                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-3 rounded text-sm">
                                        + Tambah
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
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