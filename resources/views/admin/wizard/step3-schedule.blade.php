<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Sesi Tes Baru (Langkah 3 dari 3)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8">
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg mb-6">
                        <p class="font-semibold text-green-800">Tes berhasil dibuat!</p>
                        <p class="text-sm text-green-700">Tes "<span class="font-bold">{{ $test->title }}</span>" dengan kode <span class="font-mono bg-green-200 px-1 rounded">{{ $test->test_code }}</span> telah berhasil dibuat. Langkah terakhir adalah mengatur jadwalnya.</p>
                    </div>

                    <h3 class="text-lg font-medium text-gray-900 mb-2">Atur Jadwal & Publikasi</h3>
                    <p class="text-sm text-gray-600 mb-6">Atur kapan tes ini akan tersedia untuk peserta. Anda bisa mengosongkannya jika ingin tes tersedia setiap saat.</p>

                    {{-- PERBAIKAN ADA DI BARIS ACTION DI BAWAH INI --}}
                    <form method="POST" action="{{ route('admin.wizard.post_step3', $test) }}">
                        @csrf
                        
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
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Selesai & Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
