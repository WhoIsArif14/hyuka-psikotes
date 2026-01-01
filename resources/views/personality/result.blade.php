@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Hasil: {{ $personalityTest->title }}</h1>

        <div class="p-4 border rounded">
            <p><strong>Skor total:</strong> {{ $result->score }}</p>
            <p><strong>Interpretasi:</strong> {{ $result->interpretation }}</p>
            <hr class="my-3">
            <h3 class="font-semibold">Jawaban:</h3>
            <ul class="list-disc pl-5">
                @foreach ($result->details as $qid => $val)
                    @php $q = $personalityTest->questions->where('id','=',$qid)->first(); @endphp
                    <li><strong>{{ $q->question }}:</strong> {{ $val }}</li>
                @endforeach
            </ul>
        </div>

        <a href="{{ route('personality.index') }}" class="inline-block mt-4 text-blue-600">Kembali ke daftar tes</a>
    </div>
@endsection
