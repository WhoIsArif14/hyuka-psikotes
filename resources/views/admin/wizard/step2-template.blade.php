<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Sesi Tes Baru (Langkah 2 dari 3)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Pilih Template Soal & Jenjang</h3>
                    <p class="text-sm text-gray-600 mb-6">Pilih master soal yang akan diduplikasi dan tentukan jenjangnya. Berikan juga judul baru untuk sesi tes ini.</p>

                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- PERBAIKAN ADA DI BARIS ACTION DI BAWAH INI --}}
                    <form method="POST" action="{{ route('admin.wizard.post_step2') }}">
                        @csrf
                        
                        <!-- Judul Tes Baru -->
                        <div>
                            <label for="new_test_title" class="block font-medium text-sm text-gray-700">Judul Sesi Tes Baru</label>
                            <input id="new_test_title" class="block mt-1 w-full" type="text" name="new_test_title" value="{{ old('new_test_title') }}" required autofocus />
                        </div>

                        <!-- Template & Jenjang -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label for="template_id" class="block font-medium text-sm text-gray-700">Pilih Template Soal</label>
                                <select name="template_id" id="template_id" class="block mt-1 w-full" required>
                                    <option value="">-- Pilih Master Soal --</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}">{{ $template->title }} ({{ $template->questions_count }} Soal)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenjang_id" class="block font-medium text-sm text-gray-700">Pilih Jenjang</label>
                                <select name="jenjang_id" id="jenjang_id" class="block mt-1 w-full" required>
                                     <option value="">-- Pilih Jenjang --</option>
                                    @foreach($jenjangs as $jenjang)
                                        <option value="{{ $jenjang->id }}">{{ $jenjang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <a href="{{ route('admin.wizard.step1') }}" class="text-sm text-gray-600 hover:text-gray-900">&larr; Kembali ke Langkah 1</a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Buat Tes & Lanjutkan &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
