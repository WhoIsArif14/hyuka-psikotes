<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tes PAPI Kostick (90 Pasangan)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                
                <p class="text-gray-600 mb-4">{{ __('Pilih satu dari dua pernyataan di setiap nomor yang PALING SESUAI dengan diri Anda.') }}</p>
                
                <form action="{{ route('papi.submit') }}" method="POST">
                    @csrf

                    @foreach ($items as $item)
                        <div class="card mb-4 border rounded-lg shadow-sm">
                            <div class="card-header bg-gray-50 p-3 border-b">
                                <strong>{{ __('Soal ') }}{{ $item->item_number }}</strong>
                            </div>
                            <div class="card-body p-4">
                                
                                {{-- Opsi 1: Pernyataan A --}}
                                <div class="flex items-center mb-3">
                                    <input class="form-check-input h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" type="radio" 
                                           name="item_{{ $item->item_number }}" 
                                           id="item_{{ $item->item_number }}_A" 
                                           value="A" required>
                                    <label class="ml-3 text-sm font-medium text-gray-700" for="item_{{ $item->item_number }}_A">
                                        {{ $item->statement_a }}
                                    </label>
                                </div>
                                
                                {{-- Opsi 2: Pernyataan B --}}
                                <div class="flex items-center">
                                    <input class="form-check-input h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500" type="radio" 
                                           name="item_{{ $item->item_number }}" 
                                           id="item_{{ $item->item_number }}_B" 
                                           value="B" required>
                                    <label class="ml-3 text-sm font-medium text-gray-700" for="item_{{ $item->item_number }}_B">
                                        {{ $item->statement_b }}
                                    </label>
                                </div>

                                @error("item_{$item->item_number}")
                                    <p class="text-red-500 text-xs mt-1">{{ __('Harap pilih salah satu.') }}</p>
                                @enderror
                                
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Selesaikan Tes') }}
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
