@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Laporan Modul: {{ $report['module'] }}</h1>
        <p class="text-sm text-gray-600 mb-4">{{ $report['description'] ?? '' }}</p>
        <p class="text-xs text-gray-500 mb-2">Dihasilkan: {{ $report['generated_at'] }}</p>

        @foreach ($report['sections'] as $section)
            <div class="mb-4 p-4 border rounded">
                <h2 class="font-semibold">{{ $section['title'] }}</h2>
                <p class="mt-2">{{ $section['summary'] }}</p>

                @if (!empty($section['details']))
                    <details class="mt-3">
                        <summary class="cursor-pointer text-blue-600">Tampilkan detail</summary>
                        <pre class="whitespace-pre-wrap mt-2">{{ json_encode($section['details'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </details>
                @endif
            </div>
        @endforeach

        <a href="{{ url()->previous() }}" class="inline-block mt-4 text-blue-600">Kembali</a>
    </div>
@endsection
