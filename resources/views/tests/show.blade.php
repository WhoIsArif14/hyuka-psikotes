<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Mengerjakan Tes: <span class="text-blue-600">{{ $test->title }}</span>
        </h2>
    </x-slot>

    {{-- ⚠️ WARNING POPUP - ANTI CHEATING --}}
    <div id="cheating-warning" style="display: none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); background: #ef4444; color: white; padding: 15px 30px; border-radius: 8px; z-index: 9999; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.3); animation: shake 0.5s;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 24px;">⚠️</span>
            <span id="warning-text">Peringatan!</span>
        </div>
    </div>

    {{-- 🔒 VIOLATION COUNTER --}}
    <div id="violation-counter" style="position: fixed; bottom: 20px; right: 20px; background: rgba(239, 68, 68, 0.9); color: white; padding: 12px 20px; border-radius: 8px; z-index: 9998; font-weight: bold; display: none;">
        <div style="text-align: center;">
            <div style="font-size: 12px; opacity: 0.9;">Pelanggaran</div>
            <div style="font-size: 24px;" id="violation-count">0</div>
            <div style="font-size: 10px; opacity: 0.8;">dari <span id="max-violations">5</span></div>
        </div>
    </div>

    {{-- 📖 TOMBOL LIHAT INSTRUKSI (Floating) --}}
    <button id="showInstructionsBtn" 
        class="fixed bottom-20 left-6 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-full shadow-lg z-50 transition-all duration-300 hover:scale-110"
        style="display: flex; align-items: center; gap: 8px;">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Instruksi</span>
    </button>

    {{-- 📋 MODAL INSTRUKSI AWAL (Muncul saat page load) --}}
    <div id="instructionModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto shadow-2xl">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-t-lg">
                <h2 class="text-2xl font-bold flex items-center gap-3">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Instruksi Pengerjaan Tes
                </h2>
                <p class="mt-2 text-blue-100">Baca dengan teliti sebelum memulai tes</p>
            </div>

            {{-- Content --}}
            <div class="p-6 space-y-6">
                {{-- Informasi Umum --}}
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <h3 class="font-bold text-blue-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Informasi Tes
                    </h3>
                    <ul class="space-y-1 text-sm text-gray-700">
                        <li>• <strong>Nama Tes:</strong> {{ $test->title }}</li>
                        <li>• <strong>Durasi:</strong> {{ $test->duration_minutes }} menit</li>
                        @php
                            $questions = isset($alatTes) ? $alatTes->questions : $test->questions;
                            $totalQuestions = $questions->count();
                        @endphp
                        <li>• <strong>Jumlah Soal:</strong> {{ $totalQuestions }} soal</li>
                        <li>• <strong>Sistem Penilaian:</strong> Otomatis</li>
                    </ul>
                </div>

                {{-- Peraturan Umum --}}
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <h3 class="font-bold text-red-900 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        ⚠️ PERATURAN PENTING - WAJIB DIPATUHI
                    </h3>
                    <ul class="space-y-1 text-sm text-red-800">
                        <li>✓ Tetap berada di halaman tes sampai selesai</li>
                        <li>✓ Jangan pindah tab atau minimize browser</li>
                        <li>✓ Jangan mengambil screenshot</li>
                        <li>✓ Jangan copy-paste soal/jawaban</li>
                        <li>✓ Tes akan dalam mode fullscreen</li>
                        <li class="font-bold text-red-900 mt-2">⚡ Pelanggaran akan dicatat otomatis. Maksimal 5x pelanggaran = TES DIBATALKAN!</li>
                    </ul>
                </div>

                {{-- Instruksi Per Tipe Soal --}}
                <div class="space-y-4">
                    <h3 class="font-bold text-gray-900 text-lg border-b-2 border-gray-200 pb-2">
                        📝 Jenis Soal & Cara Pengerjaan
                    </h3>

                    {{-- 1. PILIHAN GANDA --}}
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div class="bg-blue-100 text-blue-600 rounded-full p-2 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-2">1. PILIHAN GANDA</h4>
                                <p class="text-sm text-gray-700 mb-3">
                                    Pilih <strong>SATU jawaban yang paling tepat</strong> dari pilihan yang tersedia (A, B, C, D, dst).
                                </p>
                                
                                {{-- Contoh Soal PG --}}
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-xs font-semibold text-gray-600 mb-2">📌 Contoh:</p>
                                    <p class="text-sm font-medium text-gray-800 mb-2">Siapa presiden pertama Indonesia?</p>
                                    <div class="space-y-1.5 ml-3">
                                        <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-blue-50 bg-blue-100 border-blue-300">
                                            <input type="radio" checked disabled class="mr-2">
                                            <span class="text-sm">A. Ir. Soekarno ✓</span>
                                        </label>
                                        <label class="flex items-center p-2 border rounded cursor-pointer">
                                            <input type="radio" disabled class="mr-2">
                                            <span class="text-sm">B. Mohammad Hatta</span>
                                        </label>
                                        <label class="flex items-center p-2 border rounded cursor-pointer">
                                            <input type="radio" disabled class="mr-2">
                                            <span class="text-sm">C. Soeharto</span>
                                        </label>
                                    </div>
                                    <p class="text-xs text-green-700 mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Klik salah satu pilihan, lalu lanjut ke soal berikutnya
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. ESSAY --}}
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div class="bg-green-100 text-green-600 rounded-full p-2 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-2">2. ESSAY (Uraian)</h4>
                                <p class="text-sm text-gray-700 mb-3">
                                    Tulis jawaban dengan <strong>kalimat lengkap</strong> sesuai pemahaman Anda. Jawaban akan dinilai oleh admin.
                                </p>
                                
                                {{-- Contoh Soal Essay --}}
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-xs font-semibold text-gray-600 mb-2">📌 Contoh:</p>
                                    <p class="text-sm font-medium text-gray-800 mb-2">Jelaskan pentingnya pendidikan karakter bagi generasi muda!</p>
                                    <textarea 
                                        class="w-full border border-gray-300 rounded p-2 text-sm bg-white"
                                        rows="3"
                                        placeholder="Tulis jawaban Anda di sini..."
                                        disabled></textarea>
                                    <p class="text-xs text-green-700 mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Ketik jawaban lengkap dan jelas
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. HAFALAN --}}
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div class="bg-purple-100 text-purple-600 rounded-full p-2 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-2">3. HAFALAN (Memory Test)</h4>
                                <p class="text-sm text-gray-700 mb-3">
                                    Hafalkan materi yang ditampilkan dalam <strong>waktu terbatas</strong>, lalu jawab pertanyaan tanpa melihat materi lagi.
                                </p>
                                
                                {{-- Contoh Soal Hafalan --}}
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-xs font-semibold text-gray-600 mb-2">📌 Contoh:</p>
                                    
                                    {{-- Step 1: Materi Hafalan --}}
                                    <div class="bg-purple-100 border border-purple-300 rounded p-3 mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-xs font-bold text-purple-900">📚 HAFALKAN (10 detik)</p>
                                            <span class="bg-purple-600 text-white px-2 py-1 rounded text-xs font-bold">⏱️ 10</span>
                                        </div>
                                        <p class="text-sm font-medium text-gray-800">
                                            BUNGA: Dahlia, Melati, Anggrek, Mawar, Tulip
                                        </p>
                                    </div>

                                    {{-- Step 2: Pertanyaan --}}
                                    <div class="bg-white border border-gray-300 rounded p-3">
                                        <p class="text-sm font-medium text-gray-800 mb-2">Sebutkan jenis bunga yang Anda hafal! (Pilih jawaban yang benar)</p>
                                        <div class="space-y-1.5 ml-3">
                                            <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-blue-50">
                                                <input type="radio" disabled class="mr-2">
                                                <span class="text-sm">A. Dahlia, Melati, Anggrek</span>
                                            </label>
                                            <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-blue-50">
                                                <input type="radio" disabled class="mr-2">
                                                <span class="text-sm">B. Kamboja, Flamboyan, Soka</span>
                                            </label>
                                        </div>
                                    </div>

                                    <p class="text-xs text-green-700 mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Fokus hafal saat materi ditampilkan, lalu jawab pertanyaan
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 4. PAPI KOSTICK --}}
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start gap-3">
                            <div class="bg-red-100 text-red-600 rounded-full p-2 flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-900 mb-2">4. PAPI KOSTICK (Tes Kepribadian)</h4>
                                <p class="text-sm text-gray-700 mb-3">
                                    Pilih <strong>SATU pernyataan (A atau B)</strong> yang <strong>PALING menggambarkan diri Anda</strong>. Tidak ada jawaban benar/salah.
                                </p>
                                
                                {{-- Contoh Soal PAPI --}}
                                <div class="bg-gray-50 border border-gray-200 rounded p-3">
                                    <p class="text-xs font-semibold text-gray-600 mb-2">📌 Contoh:</p>
                                    <p class="text-xs text-gray-600 mb-3 italic">Pilih pernyataan yang paling menggambarkan Anda:</p>
                                    
                                    <div class="space-y-2">
                                        <label class="flex items-start p-3 border rounded cursor-pointer hover:bg-red-50 bg-red-100 border-red-300">
                                            <input type="radio" name="papi_example" checked disabled class="mr-3 mt-1">
                                            <div>
                                                <span class="font-bold text-sm">A.</span>
                                                <span class="text-sm ml-1">Saya suka bekerja dalam tim</span>
                                            </div>
                                        </label>
                                        <label class="flex items-start p-3 border rounded cursor-pointer hover:bg-red-50">
                                            <input type="radio" name="papi_example" disabled class="mr-3 mt-1">
                                            <div>
                                                <span class="font-bold text-sm">B.</span>
                                                <span class="text-sm ml-1">Saya lebih suka bekerja sendiri</span>
                                            </div>
                                        </label>
                                    </div>

                                    <p class="text-xs text-green-700 mt-3 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Jawab sesuai kepribadian Anda yang sebenarnya, bukan yang ideal
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tips Sukses --}}
                <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        💡 Tips Sukses Mengerjakan Tes
                    </h3>
                    <ul class="space-y-2 text-sm text-gray-700">
                        <li class="flex items-start gap-2">
                            <span class="text-green-600 font-bold">✓</span>
                            <span>Pastikan koneksi internet stabil</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-green-600 font-bold">✓</span>
                            <span>Baca setiap soal dengan teliti sebelum menjawab</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-green-600 font-bold">✓</span>
                            <span>Perhatikan sisa waktu di pojok kanan atas</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-green-600 font-bold">✓</span>
                            <span>Jika ragu, pilih jawaban terbaik menurut Anda</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-green-600 font-bold">✓</span>
                            <span>Klik tombol "Instruksi" di pojok kiri bawah jika perlu melihat instruksi lagi</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gray-50 px-6 py-4 rounded-b-lg flex justify-between items-center border-t">
                <label class="flex items-center text-sm text-gray-700">
                    <input type="checkbox" id="confirmReadInstructions" class="mr-2 h-4 w-4 text-blue-600 rounded">
                    Saya telah membaca dan memahami instruksi
                </label>
                <button id="startTestBtn" disabled
                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-105">
                    🚀 Mulai Tes Sekarang
                </button>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- PERINGATAN AWAL --}}
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">⚠️ PERATURAN TES - WAJIB DIBACA</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li><strong>DILARANG</strong> keluar dari halaman tes / pindah tab</li>
                                <li><strong>DILARANG</strong> mengambil screenshot (Print Screen)</li>
                                <li><strong>DILARANG</strong> copy-paste soal/jawaban</li>
                                <li><strong>DILARANG</strong> membuka Developer Tools / Inspect Element</li>
                                <li><strong>WAJIB</strong> dalam mode fullscreen selama tes</li>
                                <li class="text-red-900 font-bold">⚡ Pelanggaran akan otomatis dicatat. Maksimal 5 pelanggaran = TES DIBATALKAN!</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- INFORMASI TES & TIMER --}}
                    <div x-data="timer({{ $test->duration_minutes * 60 }})" x-init="startTimer()"
                        class="mb-8 p-4 border-l-4 border-blue-400 bg-blue-50">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $test->title }}</h3>
                                <p class="mt-1 text-sm text-gray-600">{{ $test->description }}</p>
                            </div>
                            <div class="text-2xl font-bold text-blue-800 bg-white px-4 py-2 rounded-lg shadow flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="formatTime()"></span>
                            </div>
                        </div>
                    </div>

                    {{-- FORM SOAL --}}
                    <form id="test-form" method="POST" action="{{ route('tests.store', $test) }}">
                        @csrf
                        <div class="space-y-8">
                            {{-- ✅ Cek sumber pertanyaan --}}
                            @php
                                $questions = isset($alatTes) ? $alatTes->questions : $test->questions;
                            @endphp

                            @foreach ($questions as $question)
                                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                                    {{-- BADGE TIPE SOAL --}}
                                    <div class="mb-4">
                                        @if($question->type === 'PILIHAN_GANDA')
                                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">
                                                ✓ Pilihan Ganda
                                            </span>
                                        @elseif($question->type === 'ESSAY')
                                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                                ✍️ Essay
                                            </span>
                                        @elseif($question->type === 'HAFALAN')
                                            <span class="bg-purple-100 text-purple-800 text-xs font-semibold px-3 py-1 rounded-full">
                                                📚 Hafalan
                                            </span>
                                        @elseif($question->type === 'PAPIKOSTICK')
                                            <span class="bg-red-100 text-red-800 text-xs font-semibold px-3 py-1 rounded-full">
                                                👥 PAPI Kostick
                                            </span>
                                        @endif
                                    </div>

                                    {{-- SOAL HAFALAN: Tampilkan Materi Dulu --}}
                                    @if($question->type === 'HAFALAN' && $question->memory_content)
                                        <div class="bg-purple-100 border-2 border-purple-300 rounded-lg p-4 mb-4">
                                            <div class="flex items-center justify-between mb-3">
                                                <h4 class="font-bold text-purple-900">📚 HAFALKAN MATERI INI</h4>
                                                <span class="bg-purple-600 text-white px-3 py-1 rounded-full text-sm font-bold">
                                                    ⏱️ {{ $question->duration_seconds }}s
                                                </span>
                                            </div>
                                            <div class="bg-white rounded p-3">
                                                <p class="text-gray-800 whitespace-pre-line font-medium">{{ $question->memory_content }}</p>
                                            </div>
                                            <p class="text-xs text-purple-700 mt-2 italic">
                                                * Materi akan hilang setelah {{ $question->duration_seconds }} detik, lalu Anda akan menjawab pertanyaan tanpa melihat materi ini lagi.
                                            </p>
                                        </div>
                                    @endif

                                    {{-- PERTANYAAN --}}
                                    <div class="font-semibold text-lg mb-4">
                                        <p class="flex items-start gap-2">
                                            <span class="text-blue-600">{{ $loop->iteration }}.</span>
                                            <span>{{ $question->question_text }}</span>
                                        </p>
                                        @if ($question->image_path)
                                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Gambar Soal"
                                                class="mt-4 rounded-md max-w-full md:max-w-lg"
                                                oncontextmenu="return false;" 
                                                ondragstart="return false;"
                                                style="user-select: none; -webkit-user-select: none;">
                                        @endif
                                    </div>

                                    @php
                                        // Pastikan opsi dalam format array
                                        $options = is_string($question->options)
                                            ? json_decode($question->options, true)
                                            : $question->options ?? [];
                                    @endphp

                                    {{-- OPSI JAWABAN --}}
                                    @if (is_array($options) && count($options) > 0)
                                        <div class="space-y-3">
                                            {{-- INSTRUKSI MINI untuk PAPI --}}
                                            @if($question->type === 'PAPIKOSTICK')
                                                <div class="bg-red-50 border border-red-200 rounded p-2 mb-3">
                                                    <p class="text-xs text-red-700">
                                                        <strong>Instruksi:</strong> Pilih pernyataan yang <strong>PALING menggambarkan diri Anda</strong>. Tidak ada jawaban benar atau salah.
                                                    </p>
                                                </div>
                                            @endif

                                            @foreach ($options as $option)
                                                <label
                                                    class="flex items-start p-3 border-2 rounded-md cursor-pointer hover:bg-gray-100 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-400 transition-all duration-200">
                                                    <input type="radio" name="questions[{{ $question->id }}]"
                                                        value="{{ $option['index'] ?? $loop->index }}"
                                                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500 mt-1"
                                                        required>

                                                    <div class="ml-3 text-gray-700">
                                                        <span class="font-semibold text-blue-600">{{ chr(65 + ($option['index'] ?? $loop->index)) }}.</span>
                                                        <span class="ml-2">{{ $option['text'] ?? '' }}</span>

                                                        @if (!empty($option['image_path']))
                                                            <img src="{{ asset('storage/' . $option['image_path']) }}"
                                                                alt="Gambar Opsi" class="mt-2 rounded-md max-w-xs"
                                                                oncontextmenu="return false;" 
                                                                ondragstart="return false;"
                                                                style="user-select: none; -webkit-user-select: none;">
                                                        @endif
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-red-500 text-sm">Tidak ada opsi jawaban untuk soal ini.</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" id="submit-btn"
                                class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition-all duration-300 transform hover:scale-105">
                                ✅ Selesai Mengerjakan
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- 🔒 LOAD ANTI-CHEATING SYSTEM --}}
    @push('scripts')
    <script src="{{ asset('js/anti-cheating.js') }}"></script>
    <script>
        // =============================================
        // ANTI-CHEATING SYSTEM INITIALIZATION
        // =============================================
        let antiCheating;
        let hasLeftPage = false;
        let violationCountDisplay = 0;

        document.addEventListener('DOMContentLoaded', function() {
            const testId = {{ $test->id }};
            const testResultId = {{ $testResult->id ?? 'null' }};
            const warningElement = document.getElementById('cheating-warning');
            const violationCounter = document.getElementById('violation-counter');
            const violationCountEl = document.getElementById('violation-count');
            
            // =============================================
            // MODAL INSTRUKSI (Muncul Otomatis)
            // =============================================
            const instructionModal = document.getElementById('instructionModal');
            const confirmCheckbox = document.getElementById('confirmReadInstructions');
            const startTestBtn = document.getElementById('startTestBtn');
            const showInstructionsBtn = document.getElementById('showInstructionsBtn');

            // Show modal saat page load
            instructionModal.style.display = 'flex';

            // Enable start button ketika checkbox dicentang
            confirmCheckbox.addEventListener('change', function() {
                startTestBtn.disabled = !this.checked;
            });

            // Start test button
            startTestBtn.addEventListener('click', function() {
                instructionModal.style.display = 'none';
                requestFullscreenMode();
                
                // Initialize Anti-Cheating setelah modal ditutup
                antiCheating = new AntiCheatingSystem(testId, testResultId, {
                    maxViolations: 5,
                    warningElement: warningElement,
                    onViolation: function(data) {
                        if (data && data.total_violations) {
                            violationCountDisplay = data.total_violations;
                            violationCountEl.textContent = violationCountDisplay;
                            violationCounter.style.display = 'block';

                            violationCounter.style.animation = 'shake 0.5s';
                            setTimeout(() => {
                                violationCounter.style.animation = '';
                            }, 500);
                        }
                    }
                });

                // Override showWarning
                const originalShowWarning = antiCheating.showWarning.bind(antiCheating);
                antiCheating.showWarning = function(message) {
                    const warningText = document.getElementById('warning-text');
                    if (warningText) {
                        warningText.textContent = message;
                    }
                    
                    warningElement.style.display = 'block';
                    
                    setTimeout(() => {
                        warningElement.style.display = 'none';
                    }, 5000);
                };

                console.log('✅ Anti-Cheating System Active');
            });

            // Tombol lihat instruksi lagi
            showInstructionsBtn.addEventListener('click', function() {
                instructionModal.style.display = 'flex';
            });

            // Close modal jika klik di luar
            instructionModal.addEventListener('click', function(e) {
                if (e.target === instructionModal) {
                    // Jangan tutup modal jika belum start test
                }
            });
        });

        // =============================================
        // FULLSCREEN MODE
        // =============================================
        function requestFullscreenMode() {
            const elem = document.documentElement;
            
            if (elem.requestFullscreen) {
                elem.requestFullscreen().catch(err => {
                    console.warn('Fullscreen not supported or denied');
                });
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        }

        // =============================================
        // TIMER DENGAN INTEGRASI ANTI-CHEATING
        // =============================================
        function timer(seconds) {
            return {
                timeLeft: seconds,
                startTimer() {
                    const interval = setInterval(() => {
                        this.timeLeft--;
                        if (this.timeLeft <= 0) {
                            clearInterval(interval);
                            this.timeLeft = 0;
                            if (!hasLeftPage) {
                                this.submitForm();
                            }
                        }
                    }, 1000);
                },
                formatTime() {
                    const hours = Math.floor(this.timeLeft / 3600);
                    const minutes = Math.floor((this.timeLeft % 3600) / 60);
                    const seconds = this.timeLeft % 60;

                    if (hours > 0) {
                        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    } else {
                        return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    }
                },
                submitForm() {
                    hasLeftPage = true;
                    document.getElementById('test-form').submit();
                }
            }
        }

        // =============================================
        // PREVENT FORM RESUBMISSION
        // =============================================
        document.getElementById('test-form').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Mengirim Jawaban...';
            hasLeftPage = true;
        });

        // =============================================
        // PREVENT BACK BUTTON
        // =============================================
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
            alert('❌ Tidak bisa kembali saat tes berlangsung!');
        };

        // =============================================
        // CSS ANIMATION FOR SHAKE EFFECT
        // =============================================
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(-50%) translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-50%) translateX(-10px); }
                20%, 40%, 60%, 80% { transform: translateX(-50%) translateX(10px); }
            }
            
            body {
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
            }
            
            input[type="radio"]:not(:disabled),
            input[type="text"]:not(:disabled),
            textarea:not(:disabled) {
                user-select: auto;
                -webkit-user-select: auto;
            }

            /* Smooth scroll */
            html {
                scroll-behavior: smooth;
            }
        `;
        document.head.appendChild(style);
    </script>
    @endpush
</x-app-layout>