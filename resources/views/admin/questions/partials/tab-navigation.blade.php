{{-- TAB NAVIGATION --}}
<div class="mb-6 border-b border-gray-200">
    <nav class="flex space-x-4" id="tabNav">
        <button type="button"
            class="tab-btn px-4 py-3 font-medium text-sm border-b-2 border-blue-600 text-blue-600"
            data-tab="soal">
            📝 Soal Utama
        </button>
        <button type="button"
            class="tab-btn px-4 py-3 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700"
            data-tab="contoh">
            📚 Contoh Soal
        </button>
        <button type="button"
            class="tab-btn px-4 py-3 font-medium text-sm border-b-2 border-transparent text-gray-500 hover:text-gray-700"
            data-tab="instruksi">
            ❓ Instruksi
        </button>
    </nav>
</div>

<style>
    .tab-btn {
        transition: all 0.3s ease;
    }
    .tab-btn:hover {
        border-bottom-color: #9CA3AF;
    }
    .tab-btn.active {
        border-bottom-color: #2563EB !important;
        color: #2563EB !important;
    }
</style>