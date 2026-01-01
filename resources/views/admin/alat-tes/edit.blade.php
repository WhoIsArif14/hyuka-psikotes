<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Alat Tes: {{ $AlatTes->name }}
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

                    <form method="POST" action="{{ route('admin.alat-tes.update', $AlatTes->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Nama Alat Tes --}}
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">
                                Nama Alat Tes <span class="text-red-500">*</span>
                            </label>
                            <input id="name"
                                class="block mt-1 w-full rounded-md shadow-sm {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} focus:border-indigo-500 focus:ring-indigo-500"
                                type="text" name="name" value="{{ old('name', $AlatTes->name) }}" required
                                autofocus />
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
                                class="block mt-1 w-full rounded-md shadow-sm {{ $errors->has('duration_minutes') ? 'border-red-500' : 'border-gray-300' }} focus:border-indigo-500 focus:ring-indigo-500"
                                type="number" name="duration_minutes"
                                value="{{ old('duration_minutes', $AlatTes->duration_minutes) }}" required />
                            @error('duration_minutes')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Informasi Petunjuk & Contoh Soal --}}
                        <div class="mt-4">
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                                <div class="flex items-start gap-3">
                                    <div class="flex-1">
                                        <h3 class="text-sm font-semibold text-yellow-900">Petunjuk & Contoh Soal</h3>
                                        <p class="text-xs text-yellow-800 mt-1">Instruksi dan contoh soal sekarang dikelola di halaman <strong>Kelola Soal</strong>. Silakan buka menu <strong>Kelola Soal</strong> untuk mengedit instruksi dan contoh soal.</p>
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
                                Update Alat Tes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>