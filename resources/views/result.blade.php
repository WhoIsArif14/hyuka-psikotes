<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Hasil Tes') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 md:p-12 text-gray-900 text-center">
                    
                    <h3 class="text-lg font-medium text-gray-600">
                        Anda telah menyelesaikan tes:
                    </h3>
                    <h1 class="mt-2 mb-6 text-4xl font-extrabold text-gray-800">
                        {{ $testResult->test->title }}
                    </h1>

                    <div class="inline-block bg-gray-100 rounded-lg p-6">
                        <p class="text-base font-medium text-gray-500">SKOR AKHIR ANDA</p>
                        <p class="text-7xl font-bold text-blue-600 my-2">
                            {{ $testResult->score }}
                        </p>
                    </div>

                    {{-- BAGIAN BARU: TAMPILKAN INTERPRETASI --}}
                    @if ($interpretation)
                        <div class="mt-8 text-left bg-purple-50 border-l-4 border-purple-400 p-4 rounded-r-lg">
                            <h4 class="font-bold text-purple-800">Interpretasi Hasil:</h4>
                            <p class="mt-1 text-purple-700">
                                {{ $interpretation->interpretation_text }}
                            </p>
                        </div>
                    @endif
                    {{-- ====================================== --}}

                    <div class="mt-10">
                        <p class="text-gray-600">Terima kasih telah berpartisipasi. Anda dapat melihat riwayat pengerjaan tes di halaman dashboard Anda.</p>
                        <a href="{{ route('dashboard') }}" class="mt-4 inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Kembali ke Dashboard
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>