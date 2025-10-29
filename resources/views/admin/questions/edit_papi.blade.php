<x-admin-layout>
    <x-slot name="header">
        {{-- MENGGANTI $question menjadi $papiQuestion --}}
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Soal PAPI Kostick: Item ') }}{{ $papiQuestion->item_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        {{-- Mengubah max-w-xl menjadi max-w-3xl agar form lebih leluasa --}}
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
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
                    
                    {{-- PERBAIKAN KRITIS: Ganti route update ke admin.questions.update_papi --}}
                    {{-- Ganti $AlatTes menjadi $alat_te, dan $question menjadi $papiQuestion --}}
                    <form method="POST" 
                          action="{{ route('admin.questions.update_papi', ['alat_te' => $alat_te->id, 'papi_question' => $papiQuestion->id]) }}" 
                          id="papiEditForm">
                        @csrf
                        @method('PUT')

                        {{-- Item Number --}}
                        <div class="mb-4">
                            <label for="papi_item_number" class="block text-sm font-medium text-red-700">Nomor Soal PAPI (1-90)</label>
                            <input id="papi_item_number" 
                                   name="item_number" {{-- Ganti name menjadi item_number agar sesuai dengan kolom DB --}}
                                   type="number" 
                                   min="1" max="90" 
                                   {{-- MENGGANTI $question menjadi $papiQuestion --}}
                                   value="{{ old('item_number', $papiQuestion->item_number) }}" 
                                   required
                                   class="mt-1 block w-full rounded-lg border-red-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            @error('item_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        {{-- Statements --}}
                        <h4 class="text-md font-semibold text-gray-700 mb-3 border-t pt-3 mt-3">Teks Pernyataan</h4>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="statement_a" class="block text-sm font-medium text-gray-700">Pernyataan A</label>
                                <textarea id="statement_a" name="statement_a" rows="3" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('statement_a', $papiQuestion->statement_a) }}</textarea>
                                @error('statement_a')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="statement_b" class="block text-sm font-medium text-gray-700">Pernyataan B</label>
                                <textarea id="statement_b" name="statement_b" rows="3" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('statement_b', $papiQuestion->statement_b) }}</textarea>
                                @error('statement_b')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Role and Need --}}
                        <h4 class="text-md font-semibold text-gray-700 mb-3 border-t pt-3 mt-3">Kunci Penskoran (Role & Need)</h4>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Role A / Need A --}}
                            <div>
                                <h5 class="font-bold mb-2 text-sm">Kunci Pernyataan A</h5>
                                {{-- Daftar Kode: Role (G, L, I, T, V, S, R, D, C, E), Need (N, A, P, X, B, O, Z, K, F, W) --}}
                                <label for="role_a" class="block text-xs font-medium text-gray-700">Role A (G, L, I, T, V, S, R, D, C, E)</label>
                                <input id="role_a" name="role_a" type="text" maxlength="1" 
                                        value="{{ old('role_a', $papiQuestion->role_a) }}" 
                                        required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm uppercase">
                                @error('role_a')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                
                                <label for="need_a" class="block text-xs font-medium text-gray-700 mt-3">Need A (N, A, P, X, B, O, Z, K, F, W)</label>
                                <input id="need_a" name="need_a" type="text" maxlength="1" 
                                        value="{{ old('need_a', $papiQuestion->need_a) }}" 
                                        required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm uppercase">
                                @error('need_a')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            
                            {{-- Role B / Need B --}}
                            <div>
                                <h5 class="font-bold mb-2 text-sm">Kunci Pernyataan B</h5>
                                <label for="role_b" class="block text-xs font-medium text-gray-700">Role B (G, L, I, T, V, S, R, D, C, E)</label>
                                <input id="role_b" name="role_b" type="text" maxlength="1" 
                                        value="{{ old('role_b', $papiQuestion->role_b) }}" 
                                        required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm uppercase">
                                @error('role_b')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                                
                                <label for="need_b" class="block text-xs font-medium text-gray-700 mt-3">Need B (N, A, P, X, B, O, Z, K, F, W)</label>
                                <input id="need_b" name="need_b" type="text" maxlength="1" 
                                        value="{{ old('need_b', $papiQuestion->need_b) }}" 
                                        required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm uppercase">
                                @error('need_b')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex justify-end mt-6 space-x-3 border-t pt-4">
                            {{-- Ganti $AlatTes menjadi $alat_te --}}
                            <a href="{{ route('admin.alat-tes.questions.index', $alat_te->id) }}" 
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