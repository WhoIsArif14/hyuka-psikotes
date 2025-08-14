<?php

namespace App\Exports;

use App\Models\Test;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TestResultsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $test;

    public function __construct(Test $test)
    {
        $this->test = $test;
    }

    /**
     * Menentukan query data yang akan diekspor.
     */
    public function query()
    {
        return $this->test->testResults()->with('user')->orderBy('created_at', 'desc');
    }

    /**
     * Menentukan header untuk setiap kolom di file Excel.
     */
    public function headings(): array
    {
        return [
            'ID Hasil',
            'Nama Pengguna',
            'Email Pengguna',
            'Skor',
            'Waktu Mengerjakan',
        ];
    }

    /**
     * Memetakan setiap baris data dari database ke kolom yang sesuai.
     */
    public function map($result): array
    {
        return [
            $result->id,
            $result->user->name,
            $result->user->email,
            $result->score,
            $result->created_at->format('d-m-Y H:i:s'),
        ];
    }
}