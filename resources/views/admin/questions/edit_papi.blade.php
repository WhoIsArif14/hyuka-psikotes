<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Soal PAPI Kostick: Item #') }}{{ $papiQuestion->item_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                        <strong class="font-bold">Whoops!</strong>
                        <span class="block sm:inline">Ada beberapa masalah dengan input Anda:</span>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- ✅ PERBAIKAN: Gunakan $AlatTes dan route yang benar --}}
                <form method="POST"
                    action="{{ route('admin.alat-tes.questions.update_papi', ['alat_te' => $AlatTes->id, 'papi_question' => $papiQuestion->id]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Nomor Item PAPI --}}
                    <div class="mb-4">
                        <label for="item_number" class="block text-sm font-medium text-gray-700">
                            Nomor Item PAPI (1-90) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="item_number" name="item_number" 
                            min="1" max="90" required
                            value="{{ old('item_number', $papiQuestion->item_number) }}"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                        <p class="text-xs text-gray-500 mt-1">Nomor urut soal PAPI (1-90)</p>
                    </div>

                    {{-- Pernyataan A --}}
                    <div class="mb-4">
                        <label for="statement_a" class="block text-sm font-medium text-gray-700">
                            Pernyataan A <span class="text-red-500">*</span>
                        </label>
                        <textarea id="statement_a" name="statement_a" rows="3" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Masukkan pernyataan A">{{ old('statement_a', $papiQuestion->statement_a) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Pernyataan pilihan pertama</p>
                    </div>

                    {{-- Pernyataan B --}}
                    <div class="mb-4">
                        <label for="statement_b" class="block text-sm font-medium text-gray-700">
                            Pernyataan B <span class="text-red-500">*</span>
                        </label>
                        <textarea id="statement_b" name="statement_b" rows="3" required
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                            placeholder="Masukkan pernyataan B">{{ old('statement_b', $papiQuestion->statement_b) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Pernyataan pilihan kedua</p>
                    </div>

                    {{-- Scoring Keys --}}
                    <div class="border border-red-200 bg-red-50 p-4 rounded-lg mb-4">
                        <h4 class="text-md font-semibold text-red-700 mb-3">🔑 Kunci Scoring PAPI Kostick</h4>
                        <p class="text-xs text-gray-600 mb-3">Masukkan kunci scoring untuk menghitung aspek kepribadian. Format: 1 huruf kapital (A-Z)</p>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Kunci A --}}
                            <div class="bg-white p-3 rounded-lg border border-gray-200">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Kunci Pernyataan A</p>
                                
                                <div class="mb-3">
                                    <label for="role_a" class="block text-xs font-medium text-gray-600 mb-1">
                                        Role A <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="role_a" name="role_a" 
                                        maxlength="1" required
                                        value="{{ old('role_a', $papiQuestion->role_a) }}"
                                        class="block w-full rounded border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 uppercase"
                                        placeholder="A-Z"
                                        pattern="[A-Za-z]"
                                        style="text-transform: uppercase;">
                                    <p class="text-xs text-gray-400 mt-1">1 huruf (A-Z)</p>
                                </div>

                                <div>
                                    <label for="need_a" class="block text-xs font-medium text-gray-600 mb-1">
                                        Need A <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="need_a" name="need_a" 
                                        maxlength="1" required
                                        value="{{ old('need_a', $papiQuestion->need_a) }}"
                                        class="block w-full rounded border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 uppercase"
                                        placeholder="A-Z"
                                        pattern="[A-Za-z]"
                                        style="text-transform: uppercase;">
                                    <p class="text-xs text-gray-400 mt-1">1 huruf (A-Z)</p>
                                </div>
                            </div>

                            {{-- Kunci B --}}
                            <div class="bg-white p-3 rounded-lg border border-gray-200">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Kunci Pernyataan B</p>
                                
                                <div class="mb-3">
                                    <label for="role_b" class="block text-xs font-medium text-gray-600 mb-1">
                                        Role B <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="role_b" name="role_b" 
                                        maxlength="1" required
                                        value="{{ old('role_b', $papiQuestion->role_b) }}"
                                        class="block w-full rounded border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 uppercase"
                                        placeholder="A-Z"
                                        pattern="[A-Za-z]"
                                        style="text-transform: uppercase;">
                                    <p class="text-xs text-gray-400 mt-1">1 huruf (A-Z)</p>
                                </div>

                                <div>
                                    <label for="need_b" class="block text-xs font-medium text-gray-600 mb-1">
                                        Need B <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="need_b" name="need_b" 
                                        maxlength="1" required
                                        value="{{ old('need_b', $papiQuestion->need_b) }}"
                                        class="block w-full rounded border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 uppercase"
                                        placeholder="A-Z"
                                        pattern="[A-Za-z]"
                                        style="text-transform: uppercase;">
                                    <p class="text-xs text-gray-400 mt-1">1 huruf (A-Z)</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 bg-yellow-50 border border-yellow-200 rounded p-2">
                            <p class="text-xs text-yellow-800">
                                <strong>Catatan:</strong> Role dan Need digunakan untuk menghitung aspek kepribadian PAPI Kostick. Pastikan sesuai dengan manual scoring PAPI.
                            </p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end mt-6 space-x-3 border-t pt-4">
                        <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Auto uppercase untuk input role dan need
        document.addEventListener('DOMContentLoaded', function() {
            const uppercaseInputs = document.querySelectorAll('input[pattern="[A-Za-z]"]');
            
            uppercaseInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.value = this.value.toUpperCase();
                });
            });
        });
    </script>
</x-admin-layout>