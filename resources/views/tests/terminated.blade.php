<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Test Dihentikan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    
                    {{-- Icon Warning --}}
                    <div class="mb-6">
                        <svg class="mx-auto h-24 w-24 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    
                    {{-- Title --}}
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">
                        🚫 Test Dihentikan
                    </h1>
                    
                    {{-- Message --}}
                    <p class="text-lg text-gray-600 mb-6">
                        Test Anda telah dihentikan karena terdeteksi pelanggaran berulang kali.
                    </p>
                    
                    {{-- Violation Details --}}
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 text-left">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 mb-2">
                                    Pelanggaran yang Terdeteksi:
                                </h3>
                                
                                @if(isset($violations) && $violations->count() > 0)
                                    <ul class="text-sm text-red-700 space-y-1">
                                        @foreach($violations as $violation)
                                            <li>• {{ $violation->getTypeLabel() }} 
                                                <span class="text-xs text-red-600">
                                                    ({{ $violation->occurred_at->format('H:i:s') }})
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <ul class="text-sm text-red-700">
                                        <li>• Percobaan screenshot</li>
                                        <li>• Meninggalkan halaman test</li>
                                        <li>• Membuka developer tools</li>
                                        <li>• Atau pelanggaran lainnya</li>
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    {{-- Info --}}
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-8 text-left">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Silakan hubungi administrator jika Anda merasa ini adalah kesalahan sistem atau Anda membutuhkan kesempatan untuk mengulang test.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Actions --}}
                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('dashboard') }}" 
                            class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Dashboard
                        </a>
                        
                        <a href="mailto:admin@example.com" 
                            class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Hubungi Admin
                        </a>
                    </div>
                    
                </div>
            </div>
            
            {{-- Additional Info Card --}}
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">
                    📋 Catatan Penting
                </h3>
                <ul class="text-sm text-yellow-700 space-y-2">
                    <li>✓ Pastikan Anda berada di lingkungan yang tenang dan fokus saat mengerjakan test</li>
                    <li>✓ Jangan membuka aplikasi lain atau tab browser lain selama test berlangsung</li>
                    <li>✓ Hindari menggunakan tools screenshot atau screen recording</li>
                    <li>✓ Gunakan koneksi internet yang stabil</li>
                    <li>✓ Baca instruksi test dengan seksama sebelum memulai</li>
                </ul>
            </div>
            
        </div>
    </div>
</x-app-layout>
