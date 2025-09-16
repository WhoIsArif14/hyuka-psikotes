<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tes Selesai') }}
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

                    <div class="mt-8 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
                        <p class="font-semibold text-green-800">
                            Terima kasih!
                        </p>
                        <p class="mt-1 text-green-700">
                            Jawaban Anda telah berhasil kami rekam. Anda akan segera keluar dari sistem.
                        </p>
                    </div>

                    <div class="mt-10">
                        <a href="{{ route('custom.logout') }}" class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Selesai & Keluar
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>