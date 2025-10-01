<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Alat Tes: {{ $alatTes->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.alat-tes.update', $alatTes) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Nama Alat Tes -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Nama Alat Tes</label>
                            <input id="name" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" type="text" name="name" value="{{ old('name', $alatTes->name) }}" required autofocus />
                        </div>

                        <!-- Durasi -->
                        <div class="mt-4">
                            <label for="duration_minutes" class="block font-medium text-sm text-gray-700">Durasi (dalam Menit)</label>
                            <input id="duration_minutes" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" type="number" name="duration_minutes" value="{{ old('duration_minutes', $alatTes->duration_minutes) }}" required />
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('admin.alat-tes.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>