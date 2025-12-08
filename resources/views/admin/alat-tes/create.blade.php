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
                    
                    <!-- 🚨 TAMPILKAN SEMUA ERROR (termasuk error database dari Controller) -->
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            <strong class="font-bold">Gagal Menyimpan!</strong>
                            <span class="block sm:inline">Silakan periksa input Anda dan coba lagi.</span>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li class="mt-1 text-sm">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.alat-tes.store') }}">
                        @csrf

                        <!-- Nama Alat Tes -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Nama Alat Tes</label>
                            <input id="name"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('name') border-red-500 @enderror"
                                type="text" name="name" value="{{ old('name') }}" required autofocus />
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Durasi -->
                        <div class="mt-4">
                            <label for="duration_minutes" class="block font-medium text-sm text-gray-700">Durasi (dalam
                                Menit)</label>
                            <input id="duration_minutes"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('duration_minutes') border-red-500 @enderror"
                                type="number" name="duration_minutes" value="{{ old('duration_minutes') }}" required />
                            @error('duration_minutes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instruksi Tes -->
                        <div class="mt-4">
                            <label for="instructions" class="block font-medium text-sm text-gray-700">Instruksi
                                Tes</label>
                            <textarea id="instructions" name="instructions" rows="6"
                                class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('instructions') border-red-500 @enderror"
                                placeholder="Masukkan instruksi pengerjaan tes...">{{ old('instructions') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Instruksi ini akan ditampilkan kepada peserta sebelum
                                memulai tes.</p>
                            @error('instructions')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.alat-tes.index') }}"
                                class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>