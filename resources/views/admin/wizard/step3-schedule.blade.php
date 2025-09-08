<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Buat Sesi Tes Baru (Langkah 3 dari 3)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Atur Jadwal Tes</h3>

                <form method="POST" action="{{ route('admin.wizard.step3.post', $test->id) }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Tanggal Mulai</label>
                        <input type="datetime-local" name="available_from" class="block mt-1 w-full border rounded"
                               value="{{ old('available_from', $test->available_from) }}">
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Tanggal Selesai</label>
                        <input type="datetime-local" name="available_to" class="block mt-1 w-full border rounded"
                               value="{{ old('available_to', $test->available_to) }}">
                    </div>

                    <div class="mb-4 flex items-center">
                        <input type="checkbox" name="is_published" id="is_published"
                               {{ $test->is_published ? 'checked' : '' }}>
                        <label for="is_published" class="ml-2 text-sm text-gray-700">Publikasikan Tes</label>
                    </div>

                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Simpan & Selesaikan
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
