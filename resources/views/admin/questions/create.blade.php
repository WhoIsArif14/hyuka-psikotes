<x-admin-layout>
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Tambah Soal Baru</h2>

        <form action="{{ route('admin.questions.store', $alatTes->id) }}" method="POST" enctype="multipart/form-data" id="form-soal">
            @csrf

            <!-- Tipe Pertanyaan -->
            <div class="mb-4">
                <label for="type" class="block font-medium mb-2">Tipe Pertanyaan</label>
                <select name="type" id="type" class="border-gray-300 rounded-lg w-full">
                    <option value="pilihan_ganda">Pilihan Ganda</option>
                    <option value="esai">Esai</option>
                </select>
            </div>

            <!-- Upload Gambar Pertanyaan -->
            <div class="mb-4">
                <label class="block font-medium mb-2">Upload Gambar Pertanyaan (Opsional)</label>
                <input type="file" name="question_image" accept=".jpg,.jpeg,.png,.gif"
                    class="border border-gray-300 rounded-lg p-2 w-full">
                <small class="text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB</small>
            </div>

            <!-- Teks Pertanyaan -->
            <div class="mb-4">
                <label class="block font-medium mb-2">Teks Pertanyaan</label>
                <textarea name="question_text" rows="3" class="border-gray-300 rounded-lg w-full" placeholder="Masukkan teks pertanyaan di sini."></textarea>
            </div>

            <!-- Opsi Jawaban -->
            <div id="opsi-container" class="mb-4">
                <label class="block font-medium mb-2">Opsi Jawaban</label>

                <!-- Template Opsi -->
                <div class="opsi-item border rounded-lg p-4 mb-2 bg-gray-50">
                    <div class="flex items-center mb-2">
                        <input type="radio" name="correct_option" class="mr-2" value="0">
                        <span class="mr-2 font-semibold">Opsi A</span>
                        <button type="button" class="remove-opsi text-red-500 ml-auto hidden">✕</button>
                    </div>

                    <!-- Input teks opsi -->
                    <input type="text" name="options[0][text]" class="border-gray-300 rounded-lg w-full mb-2"
                        placeholder="Masukkan teks untuk Opsi A">

                    <!-- Input upload gambar untuk opsi -->
                    <input type="file" name="options[0][image]" accept=".jpg,.jpeg,.png,.gif"
                        class="border-gray-300 rounded-lg w-full">
                    <small class="text-gray-500">Opsional: upload gambar untuk opsi ini</small>
                </div>
            </div>

            <button type="button" id="add-option" class="bg-green-500 text-white px-3 py-1 rounded-lg mb-4">+ Tambah Opsi</button>

            <div class="flex justify-end">
                <a href="{{ route('admin.alat-tes.questions.index', $alatTes->id) }}" class="px-4 py-2 bg-gray-300 rounded-lg mr-2">Bata</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Simpan Pertanyaan</button>
            </div>
        </form>
    </div>

    <script>
        let optionCount = 1;
        const container = document.getElementById('opsi-container');
        const addOptionBtn = document.getElementById('add-option');

        addOptionBtn.addEventListener('click', function() {
            const label = String.fromCharCode(65 + optionCount); // A, B, C, D...
            const index = optionCount;

            const newOpsi = document.createElement('div');
            newOpsi.classList.add('opsi-item', 'border', 'rounded-lg', 'p-4', 'mb-2', 'bg-gray-50');
            newOpsi.innerHTML = `
                <div class="flex items-center mb-2">
                    <input type="radio" name="correct_option" class="mr-2" value="${index}">
                    <span class="mr-2 font-semibold">Opsi ${label}</span>
                    <button type="button" class="remove-opsi text-red-500 ml-auto">✕</button>
                </div>
                <input type="text" name="options[${index}][text]" class="border-gray-300 rounded-lg w-full mb-2"
                    placeholder="Masukkan teks untuk Opsi ${label}">
                <input type="file" name="options[${index}][image]" accept=".jpg,.jpeg,.png,.gif"
                    class="border-gray-300 rounded-lg w-full">
                <small class="text-gray-500">Opsional: upload gambar untuk opsi ini</small>
            `;

            container.appendChild(newOpsi);

            // Tambahkan event hapus
            newOpsi.querySelector('.remove-opsi').addEventListener('click', function() {
                newOpsi.remove();
                updateOptionLabels();
            });

            optionCount++;
        });

        function updateOptionLabels() {
            const items = document.querySelectorAll('.opsi-item');
            items.forEach((item, idx) => {
                const label = String.fromCharCode(65 + idx);
                item.querySelector('span').textContent = `Opsi ${label}`;
                item.querySelector('input[type="radio"]').value = idx;
                item.querySelector('input[type="text"]').name = `options[${idx}][text]`;
                item.querySelector('input[type="file"]').name = `options[${idx}][image]`;
            });
        }
    </script>
</x-admin-layout>
