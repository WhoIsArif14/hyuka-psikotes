<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Modul: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- INFORMASI MODUL --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-bold">{{ $test->title }}</h3>
                            <p class="mt-2 text-blue-100">{{ $test->description }}</p>
                        </div>
                        <div class="text-center bg-white/20 backdrop-blur-sm rounded-lg p-4">
                            <div class="text-3xl font-bold">{{ $completedCount }} / {{ $totalAlatTes }}</div>
                            <div class="text-sm text-blue-100">Alat Tes Selesai</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- PROGRESS BAR --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold text-gray-700">Progress Keseluruhan</span>
                        <span class="text-sm font-bold text-blue-600">{{ $progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-4 rounded-full transition-all duration-500 flex items-center justify-end px-2"
                             style="width: {{ $progressPercentage }}%">
                            @if($progressPercentage > 10)
                                <span class="text-xs font-bold text-white">{{ $progressPercentage }}%</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- PETUNJUK --}}
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700 font-semibold">
                            Anda bebas memilih alat tes mana yang ingin dikerjakan terlebih dahulu. Klik tombol "Mulai Tes" untuk memulai pengerjaan.
                        </p>
                    </div>
                </div>
            </div>

            {{-- LIST ALAT TES --}}
            <div class="space-y-4">
                @foreach($alatTesList as $index => $alatTes)
                    @php
                        $isCompleted = in_array($alatTes->id, $completedAlatTesIds);
                        $isAvailable = !$isCompleted; // Semua yang belum selesai bisa diakses
                    @endphp

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 transition-all duration-200
                                {{ $isCompleted ? 'border-green-300 bg-green-50/30' : 'border-blue-300 hover:shadow-lg' }}">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                {{-- KIRI: Nomor & Info Alat Tes --}}
                                <div class="flex items-start gap-4 flex-1">
                                    {{-- Nomor Urut --}}
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg
                                                    {{ $isCompleted ? 'bg-green-500 text-white' : 'bg-blue-500 text-white' }}">
                                            @if($isCompleted)
                                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Info Alat Tes --}}
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-bold text-gray-900">{{ $alatTes->name }}</h3>

                                            @if($isCompleted)
                                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Selesai
                                                </span>
                                            @endif
                                        </div>

                                        @if($alatTes->description)
                                            <p class="text-sm text-gray-600 mb-3">{{ $alatTes->description }}</p>
                                        @endif

                                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                            <div class="flex items-center gap-1">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $alatTes->duration_minutes }} menit</span>
                                            </div>

                                            @php
                                                $questionCount = $alatTes->questions->count();
                                                if ($questionCount == 0) {
                                                    // Check for PAPI or RMIB
                                                    $slug = strtolower($alatTes->slug ?? $alatTes->name ?? '');
                                                    if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
                                                        $questionCount = \App\Models\PapiKostickItem::count();
                                                    } elseif (str_contains($slug, 'rmib')) {
                                                        $questionCount = \App\Models\RmibItem::count();
                                                    }
                                                }
                                            @endphp

                                            @if($questionCount > 0)
                                                <div class="flex items-center gap-1">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                    </svg>
                                                    <span>{{ $questionCount }} soal</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- KANAN: Tombol Aksi --}}
                                <div class="flex-shrink-0 ml-4">
                                    @if($isCompleted)
                                        <button disabled
                                                class="bg-green-100 text-green-700 font-bold py-3 px-6 rounded-lg cursor-not-allowed opacity-75 flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Sudah Selesai
                                        </button>
                                    @else
                                        <a href="{{ route('tests.preparation', ['test' => $test->id, 'alat_tes' => $alatTes->id]) }}"
                                           class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 hover:shadow-lg flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                            </svg>
                                            Mulai Tes
                                        </a>
                                    @endif
                                </div>
                            </div>

                            {{-- Progress Indicator untuk yang sudah selesai --}}
                            @if($isCompleted)
                                <div class="mt-4 pt-4 border-t border-green-200">
                                    <div class="flex items-center gap-2 text-sm text-green-700">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-medium">Diselesaikan pada:
                                            @php
                                                $result = null;
                                                $slug = strtolower($alatTes->slug ?? $alatTes->name ?? '');
                                                if (in_array($slug, ['papi-kostick', 'papikostick', 'papi_kostick', 'papi kostick'])) {
                                                    $result = \App\Models\PapiResult::where('user_id', auth()->id())->first();
                                                } elseif (str_contains($slug, 'rmib')) {
                                                    $result = \App\Models\RmibResult::where('user_id', auth()->id())
                                                        ->where('alat_tes_id', $alatTes->id)->first();
                                                } else {
                                                    $result = \App\Models\TestResult::where('user_id', auth()->id())
                                                        ->where('alat_tes_id', $alatTes->id)->first();
                                                }
                                            @endphp
                                            {{ $result ? $result->created_at->format('d M Y, H:i') : '-' }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- TOMBOL LOGOUT --}}
            <div class="mt-8 text-center">
                <form method="POST" action="{{ route('logout') }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin keluar? Progress tes Anda yang belum diselesaikan akan tersimpan.')">
                    @csrf
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-8 rounded-lg transition-all duration-200 transform hover:scale-105 flex items-center gap-2 mx-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</x-app-layout>
