<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tes Selesai
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <!-- Success Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center">
                    <!-- Icon Success -->
                    <div class="mb-6">
                        <svg class="mx-auto h-24 w-24 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>

                    <!-- Title -->
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">
                        🎉 Terima Kasih!
                    </h1>

                    <!-- Message -->
                    <div class="text-gray-600 space-y-3 mb-8">
                        <p class="text-lg">
                            Tes Anda telah <strong>berhasil diselesaikan</strong> dan jawaban telah tersimpan.
                        </p>
                        <p>
                            Hasil tes akan diproses oleh administrator dan dapat Anda lihat melalui dashboard atau email yang telah Anda daftarkan.
                        </p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8 text-left rounded">
                        <h3 class="font-bold text-blue-800 mb-2">📌 Informasi Penting:</h3>
                        <ul class="list-disc list-inside text-blue-700 space-y-1 text-sm">
                            <li>Hasil tes akan direview oleh tim kami</li>
                            <li>Anda akan menerima notifikasi melalui email</li>
                            <li>Hasil lengkap dapat diakses melalui dashboard Anda</li>
                            <li>Jika ada pertanyaan, silakan hubungi administrator</li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <a href="{{ route('dashboard') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition w-full justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                            Kembali ke Dashboard
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition w-full justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Additional Info Card -->
            <div class="mt-6 bg-gray-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-700 mb-3">💼 Langkah Selanjutnya</h3>
                    <ol class="list-decimal list-inside text-gray-600 space-y-2 text-sm">
                        <li>Tunggu email konfirmasi dari kami (biasanya dalam 1-3 hari kerja)</li>
                        <li>Cek dashboard Anda secara berkala untuk update hasil</li>
                        <li>Jika hasil sudah tersedia, Anda dapat mengunduh laporan lengkap</li>
                        <li>Hubungi admin jika ada pertanyaan atau kendala</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>