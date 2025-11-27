<div class="mb-4 border border-indigo-200 bg-indigo-50 p-4 rounded-lg">
    <h4 class="text-md font-semibold text-indigo-700 mb-3">📊 Kategori Perangkingan</h4>
    
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label for="ranking_category" class="block text-sm font-medium text-gray-700">
                Kategori
            </label>
            <select id="ranking_category" name="ranking_category" class="mt-1 block w-full rounded-lg">
                <option value="">-- Tanpa Kategori --</option>
                <option value="LOGIKA">Logika</option>
                <option value="VERBAL">Verbal</option>
                {{-- ... options lainnya --}}
            </select>
        </div>
        
        <div>
            <label for="ranking_weight" class="block text-sm font-medium text-gray-700">
                Bobot Soal
            </label>
            <input type="number" id="ranking_weight" name="ranking_weight" 
                   min="1" max="100" value="{{ old('ranking_weight', 1) }}"
                   class="mt-1 block w-full rounded-lg">
        </div>
    </div>
</div>