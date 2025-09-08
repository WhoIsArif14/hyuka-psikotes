<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Tes: ') . $test->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900">
                    
                    <form method="POST" action="{{ route('admin.tests.update', $test) }}">
                        @csrf
                        @method('PUT')

                        <!-- Judul Tes -->
                        <div>
                            <label for="title" class="block font-medium text-sm text-gray-700">Judul Tes</label>
                            <input id="title" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="text" name="title" value="{{ old('title', $test->title) }}" required autofocus />
                        </div>

                        <!-- Kategori & Jenjang -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label for="test_category_id" class="block font-medium text-sm text-gray-700">Kategori Tes</label>
                                <select name="test_category_id" id="test_category_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('test_category_id', $test->test_category_id) == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="jenjang_id" class="block font-medium text-sm text-gray-700">Jenjang</label>
                                <select name="jenjang_id" id="jenjang_id" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">
                                    @foreach($jenjangs as $jenjang)
                                        <option value="{{ $jenjang->id }}" @selected(old('jenjang_id', $test->jenjang_id) == $jenjang->id)>{{ $jenjang->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mt-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Deskripsi/Instruksi</label>
                            <textarea id="description" name="description" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" rows="4">{{ old('description', $test->description) }}</textarea>
                        </div>
                        
                        <!-- Durasi & Kode Tes -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label for="duration_minutes" class="block font-medium text-sm text-gray-700">Durasi (dalam Menit)</label>
                                <input id="duration_minutes" class="block mt-1 w-full rounded-md shadow-sm border-gray-300" type="number" name="duration_minutes" value="{{ old('duration_minutes', $test->duration_minutes) }}" required />
                            </div>
                            <div>
                                <label for="test_code" class="block font-medium text-sm text-gray-700">Kode Tes</label>
                                <input id="test_code" class="block mt-1 w-full font-mono bg-gray-100" type="text" name="test_code" value="{{ old('test_code', $test->test_code) }}" readonly />
                                <small class="text-gray-500">Kode tes tidak dapat diubah.</small>
                            </div>
                        </div>

                        <!-- Jadwal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                             <div>
                                <label for="available_from" class="block font-medium text-sm text-gray-700">Tersedia Mulai (Opsional)</label>
                                <input id="available_from" class="block mt-1 w-full" type="datetime-local" name="available_from" value="{{ old('available_from', $test->available_from) }}" />
                            </div>
                            <div>
                                <label for="available_to" class="block font-medium text-sm text-gray-700">Tersedia Hingga (Opsional)</label>
                                <input id="available_to" class="block mt-1 w-full" type="datetime-local" name="available_to" value="{{ old('available_to', $test->available_to) }}" />
                            </div>
                        </div>

                        <!-- Opsi Checkbox -->
                        <div class="mt-4 space-y-2">
                             <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm" name="is_published" value="1" @checked(old('is_published', $test->is_published))>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Publikasikan Tes') }}</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm" name="is_template" value="1" @checked(old('is_template', $test->is_template))>
                                <span class="ml-2 text-sm text-gray-600">{{ __('Jadikan sebagai Template/Master') }}</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.tests.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
