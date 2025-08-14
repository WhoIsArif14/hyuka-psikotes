<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Soal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.questions.update', $question) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="question_text" class="block font-medium text-sm text-gray-700">Teks Soal</label>
                            <textarea name="question_text" id="question_text" rows="5" class="block mt-1 w-full rounded-md shadow-sm border-gray-300">{{ old('question_text', $question->question_text) }}</textarea>
                        </div>
                        <div class="flex justify-end mt-4">
                            <a href="{{ route('admin.tests.questions.index', $question->test_id) }}" class="text-gray-600 hover:text-gray-900 mr-4">Batal</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>