<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Soal PAPI Kostick: Item ') }}{{ $question->item_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
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

                <div class="bg-white p-6 rounded-xl border border-red-300">
                    
                    {{-- PERBAIKAN PENTING DI SINI: Action URL HARUS MENGGUNAKAN route update dengan 2 parameter --}}
                    <form method="POST" 
                          action="{{ route('admin.alat-tes.questions.update', ['alat_te' => $AlatTes->id, 'question' => $question->id]) }}" 
                          id="papiEditForm">
                        @csrf
                        @method('PUT')

                        {{-- Item Number --}}
                        <div class="mb-4">
                            <label for="papi_item_number" class="block text-sm font-medium text-red-700">Nomor Soal PAPI (1-90)</label>
                            <input id="papi_item_number" 
                                   name="papi_item_number" 
                                   type="number" 
                                   min="1" max="90" 
                                   value="{{ old('papi_item_number', $question->item_number) }}" 
                                   required
                                   class="mt-1 block w-full rounded-lg border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            @error('papi_item_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Statements --}}
                        <h4 class="text-md font-semibold text-gray-700 mb-3 border-t pt-3 mt-3">Teks Pernyataan</h4>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="statement_a" class="block text-sm font-medium text-gray-700">Pernyataan A</label>
                                <textarea id="statement_a" name="statement_a" rows="3" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('statement_a', $question->statement_a) }}</textarea>
                                @error('statement_a')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="statement_b" class="block text-sm font-medium text-gray-700">Pernyataan B</label>
                                <textarea id="statement_b" name="statement_b" rows="3" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('statement_b', $question->statement_b) }}</textarea>
                                @error('statement_b')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Role and Need --}}
                        <h4 class="text-md font-semibold text-gray-700 mb-3 border-t pt-3 mt-3">Kunci Penskoran (Role & Need)</h4>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Role A / Need A --}}
                            <div>
                                <h5 class="font-bold mb-2 text-sm">Kunci Pernyataan A</h5>
                                <label for="role_a" class="block text-xs font-medium text-gray-700">Role A (G, L, I, T, V, S, R, D, C, E)</label>
                                <input id="role_a" name="role_a" type="text" maxlength="1" 
                                       value="{{ old('role_a', $question->role_a) }}" 
                                       required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm uppercase">
                                @error('role_a')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                
                                <label for="need_a" class="block text-xs font-medium text-gray-700 mt-3">Need A (N, A, P, X, B, O, Z, K, F, W)</label>
                                <input id="need_a" name="need_a" type="text" maxlength="1" 
                                       value="{{ old('need_a', $question->need_a) }}" 
                                       required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm uppercase">
                                @error('need_a')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            
                            {{-- Role B / Need B --}}
                            <div>
                                <h5 class="font-bold mb-2 text-sm">Kunci Pernyataan B</h5>
                                <label for="role_b" class="block text-xs font-medium text-gray-700">Role B (G, L, I, T, V, S, R, D, C, E)</label>
                                <input id="role_b" name="role_b" type="text" maxlength="1" 
                                       value="{{ old('role_b', $question->role_b) }}" 
                                       required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm uppercase">
                                @error('role_b')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                
                                <label for="need_b" class="block text-xs font-medium text-gray-700 mt-3">Need B (N, A, P, X, B, O, Z, K, F, W)</label>
                                <input id="need_b" name="need_b" type="text" maxlength="1" 
                                       value="{{ old('need_b', $question->need_b) }}" 
                                       required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm uppercase">
                                @error('need_b')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end mt-6 space-x-3 border-t pt-4">
                            <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}" 
                               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-md">
                                Batal
                            </a>
                            <button type="submit"
                                    class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition">
                                Update Soal PAPI
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>