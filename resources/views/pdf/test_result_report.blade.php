<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        .small {
            font-size: 11px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN HASIL PSIKOTES</h2>
    </div>

    <div class="section">
        <table>
            <tr>
                <td style="width: 150px;"><strong>Nama Peserta</strong></td>
                <td>{{ $testResult->participant_name ?? ($testResult->user->name ?? '—') }}</td>
            </tr>
            <tr>
                <td><strong>Email / No. HP</strong></td>
                <td>{{ $testResult->participant_email ?? ($testResult->user->email ?? '-') }} /
                    {{ $testResult->phone_number ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Modul Tes</strong></td>
                <td>{{ $testResult->test->title ?? 'Modul' }}</td>
            </tr>
            <tr>
                <td><strong>Kode Tes</strong></td>
                <td>{{ $testResult->test->test_code ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Kode Peserta</strong></td>
                <td>{{ $activationCode->code ?? '-' }}</td>
            </tr>
        </table>
        <div style="margin-top: 10px;">
            <strong>Skor:</strong> {{ $testResult->score ?? '-' }} &nbsp;&nbsp; <strong>IQ:</strong>
            {{ $testResult->iq ?? '-' }} @if ($testResult->iq)
                ({{ $testResult->iq_interpretation }})
            @endif
        </div>
    </div>

    @if ($papiResult)
        <div class="section">
            <strong>Hasil PAPI Kostick (Skor Mentah)</strong>
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Aspek</th>
                        <th>Skor</th>
                    </tr>
                </thead>
                <tbody>
                    @php $dimensions = App\Models\PapiQuestion::getDimensions(); @endphp
                    @foreach ($orderedScores as $kode => $score)
                        <tr>
                            <td>{{ $kode }}</td>
                            <td>{{ $dimensions[$kode] ?? '-' }}</td>
                            <td>{{ $score }} / 9</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="section small">
        <p>Dokumen dihasilkan oleh sistem pada {{ now()->format('d M Y H:i') }}.</p>
    </div>
</body>

</html>
