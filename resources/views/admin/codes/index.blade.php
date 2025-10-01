<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Generate Kode Aktivasi Peserta
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p class="font-bold">Sukses!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Form Generate Kode Baru -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Buat Kode Aktivasi Massal</h3>
                    <p class="text-sm text-gray-600 mb-4">Pilih tes dan masukkan jumlah peserta untuk membuat kode aktivasi. Semua kode akan berlaku selama 24 jam.</p>
                    
                    <form method="POST" action="{{ route('admin.codes.store') }}">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Pilih Tes -->
                            <div class="md:col-span-2">
                                <label for="test_id" class="block text-sm font-medium text-gray-700">Pilih Tes</label>
                                <select name="test_id" id="test_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-- Pilih salah satu tes --</option>
                                    @foreach($tests as $test)
                                        <option value="{{ $test->id }}">{{ $test->title }} (Kode: {{ $test->test_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Jumlah Kode -->
                            <div>
                                <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah Kode</label>
                                <input type="number" name="quantity" id="quantity" required min="1" max="1000" value="50" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="w-full sm:w-auto inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Buat Kode
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Menampilkan Kode yang Baru Dibuat -->
            @if (session('generated_codes'))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                         <h3 class="text-lg font-medium text-gray-900 mb-4">Kode Baru (Salin & Bagikan):</h3>
                         <textarea readonly class="w-full h-48 font-mono text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50">{{ implode("\n", session('generated_codes')) }}</textarea>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
