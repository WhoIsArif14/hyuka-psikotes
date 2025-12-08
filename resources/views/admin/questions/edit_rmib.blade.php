<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Item RMIB: ') }}{{ $AlatTes->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @include('admin.questions.partials.form-header')

            {{-- Form Edit RMIB --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">✏️ Edit Item RMIB</h3>
                        @if ($question->rmibItem)
                            <p class="text-sm text-gray-600 mt-1">
                                Item #{{ $question->rmibItem->item_number }} -
                                <span class="font-semibold">{{ $question->rmibItem->interest_area_name }}</span>
                            </p>
                        @endif
                    </div>
                    <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg">
                        ← Kembali
                    </a>
                </div>

                <form method="POST"
                    action="{{ route('admin.alat-tes.questions.rmib.update', [$AlatTes->id, $question->id]) }}"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Info Item RMIB --}}
                    @if ($question->rmibItem)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <h4 class="font-semibold text-green-900 mb-2">📋 Informasi Item</h4>
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Nomor Item:</p>
                                    <p class="font-bold text-green-700 text-lg">{{ $question->rmibItem->item_number }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Bidang Minat:</p>
                                    <p class="font-semibold text-gray-800">{{ $question->rmibItem->interest_area_name }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Profesi/Aktivitas:</p>
                                    <p class="font-semibold text-gray-800">{{ $question->rmibItem->description }}</p>
                                </div>
                            </div>
                            <div class="mt-3 text-xs text-green-700">
                                <strong>ℹ️ Catatan:</strong> Nomor item dan bidang minat tidak dapat diubah. Gunakan
                                field di bawah untuk custom deskripsi atau tambahan informasi.
                            </div>
                        </div>
                    @endif

                    {{-- Custom Question Text (Optional) --}}
                    <div class="mb-4">
                        <label for="question_text" class="block text-sm font-medium text-gray-700">
                            Deskripsi Custom (Opsional)
                        </label>
                        <textarea id="question_text" name="question_text" rows="3"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Kosongkan jika ingin menggunakan deskripsi standar dari master data">{{ old('question_text', $question->rmibItem ? $question->rmibItem->description : $question->question_text) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            Jika diisi, akan menggantikan deskripsi standar:
                            "{{ $question->rmibItem ? $question->rmibItem->description : '-' }}"
                        </p>
                        @error('question_text')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Current Image --}}
                    @if ($question->image_path)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini:</label>
                            <div class="relative inline-block">
                                <img src="{{ asset('storage/' . $question->image_path) }}" alt="Current Image"
                                    class="max-w-xs rounded-lg border border-gray-300">
                                <button type="button"
                                    onclick="if(confirm('Hapus gambar ini?')) document.getElementById('delete_image').value = '1'; this.parentElement.remove();"
                                    class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" id="delete_image" name="delete_image" value="0">
                        </div>
                    @endif

                    {{-- Upload New Image --}}
                    <div class="mb-4">
                        <label for="question_image" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $question->image_path ? 'Ganti Gambar (Opsional)' : 'Upload Gambar (Opsional)' }}
                        </label>

                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2 mb-2 flex items-start gap-2">
                            <svg class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <p class="text-xs text-yellow-800">
                                Maksimal <strong class="text-red-600">5 MB</strong>
                            </p>
                        </div>

                        <input type="file" id="question_image" name="question_image" accept="image/*"
                            class="image-upload-input block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100" />
                        <div class="image-preview mt-3 hidden">
                            <img src="" alt="Preview"
                                class="preview-img max-w-xs rounded-lg border border-gray-300">
                            <button type="button"
                                class="remove-image-btn text-red-600 hover:text-red-800 text-sm font-medium mt-2">
                                Hapus Preview
                            </button>
                        </div>

                        @error('question_image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @include('admin.questions.partials.tab-navigation')

                    {{-- TAB CONTENT --}}
                    <div class="tab-contents">
                        {{-- TAB SOAL UTAMA --}}
                        <div class="tab-content" id="tab-soal">
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <p class="text-sm text-blue-700">
                                    ℹ️ Informasi dasar item (nomor, bidang minat, profesi) diambil dari master data dan
                                    tidak dapat diubah di sini.
                                    Gunakan field di atas untuk menambahkan deskripsi custom atau gambar tambahan.
                                </p>
                            </div>
                        </div>

                        {{-- TAB CONTOH SOAL --}}
                        <div class="tab-content hidden" id="tab-contoh">
                            <div class="mb-4">
                                <label for="example_question" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contoh Item RMIB (Opsional)
                                </label>
                                <textarea id="example_question" name="example_question" rows="12"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 font-mono text-sm">{{ old('example_question', $question->example_question) }}</textarea>
                            </div>
                        </div>

                        {{-- TAB INSTRUKSI --}}
                        <div class="tab-content hidden" id="tab-instruksi">
                            <div class="mb-4">
                                <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                    Instruksi & Cara Menjawab (Opsional)
                                </label>
                                <textarea id="instructions" name="instructions" rows="10"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500">{{ old('instructions', $question->instructions) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <a href="{{ route('admin.alat-tes.questions.index', $AlatTes->id) }}"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg">
                            Batal
                        </a>
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg">
                            💾 Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Image preview
                const imageInput = document.getElementById('question_image');
                const imagePreview = document.querySelector('.image-preview');
                const previewImg = document.querySelector('.preview-img');
                const removeBtn = document.querySelector('.remove-image-btn');
                const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB

                if (imageInput) {
                    imageInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            if (file.size > MAX_FILE_SIZE) {
                                alert('❌ FILE TERLALU BESAR!\n\nUkuran file: ' + (file.size / (1024 * 1024))
                                    .toFixed(2) + ' MB\nMaksimal: 5 MB');
                                e.target.value = '';
                                if (imagePreview) imagePreview.classList.add('hidden');
                                return;
                            }

                            const reader = new FileReader();
                            reader.onload = function(e) {
                                if (previewImg) previewImg.src = e.target.result;
                                if (imagePreview) imagePreview.classList.remove('hidden');
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                }

                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        if (imageInput) imageInput.value = '';
                        if (imagePreview) imagePreview.classList.add('hidden');
                    });
                }

                // Tab navigation
                const tabButtons = document.querySelectorAll('.tab-btn');
                const tabContents = document.querySelectorAll('.tab-content');

                tabButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const targetTab = this.getAttribute('data-tab');

                        tabButtons.forEach(btn => {
                            btn.classList.remove('active', 'border-blue-600', 'text-blue-600');
                            btn.classList.add('border-transparent', 'text-gray-500');
                        });

                        this.classList.add('active', 'border-blue-600', 'text-blue-600');
                        this.classList.remove('border-transparent', 'text-gray-500');

                        tabContents.forEach(content => content.classList.add('hidden'));

                        const targetContent = document.getElementById('tab-' + targetTab);
                        if (targetContent) targetContent.classList.remove('hidden');
                    });
                });

                if (tabButtons.length > 0) {
                    tabButtons[0].click();
                }
            });
        </script>
    @endpush

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
</x-admin-layout>
