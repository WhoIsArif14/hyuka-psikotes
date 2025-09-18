<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Jenjang;
use App\Models\Test;
use App\Models\TestCategory;
use Illuminate\Http\Request;

// --- PERUBAHAN UTAMA ADA DI SINI ---
// Hapus 'use' untuk Maatwebsite dan ganti dengan PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
// ------------------------------------

class TestController extends Controller
{
    public function index()
    {
        $tests = Test::with(['category', 'jenjang', 'client'])
                     ->withCount('questions')
                     ->latest()
                     ->paginate(10);
        return view('admin.tests.index', compact('tests'));
    }

    public function create()
    {
        $clients = Client::all();
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();
        return view('admin.tests.create', compact('clients', 'categories', 'jenjangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'test_category_id' => 'required|exists:test_categories,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'test_code' => 'nullable|string|max:8|unique:tests,test_code',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
        ]);

        $data = $request->all();
        $data['is_published'] = $request->has('is_published');
        $data['is_template'] = $request->has('is_template');

        Test::create($data);

        return redirect()->route('admin.tests.index')->with('success', 'Tes baru berhasil ditambahkan.');
    }

    public function edit(Test $test)
    {
        $clients = Client::all();
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();
        return view('admin.tests.edit', compact('test', 'clients', 'categories', 'jenjangs'));
    }

    public function update(Request $request, Test $test)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'test_category_id' => 'required|exists:test_categories,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'test_code' => 'nullable|string|max:8|unique:tests,test_code,' . $test->id,
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
        ]);

        $data = $request->all();
        $data['is_published'] = $request->has('is_published');
        $data['is_template'] = $request->has('is_template');

        $test->update($data);

        return redirect()->route('admin.tests.index')->with('success', 'Tes berhasil diperbarui.');
    }

    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.tests.index')->with('success', 'Tes berhasil dihapus.');
    }
    
    public function results(Test $test)
    {
        $results = $test->testResults()->latest()->paginate(15);
        return view('admin.tests.results', compact('test', 'results'));
    }

    /**
     * Mengekspor hasil tes ke file Excel menggunakan PhpSpreadsheet.
     */
    public function export(Test $test)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Menulis Header
        $sheet->setCellValue('A1', 'Nama Peserta');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'No. HP');
        $sheet->setCellValue('D1', 'Pendidikan');
        $sheet->setCellValue('E1', 'Jurusan');
        $sheet->setCellValue('F1', 'Skor');
        $sheet->setCellValue('G1', 'Waktu Mengerjakan');

        // Mengambil data hasil tes
        $results = $test->testResults()->get();
        $rowNumber = 2; // Mulai dari baris kedua

        // Menulis data untuk setiap hasil
        foreach ($results as $result) {
            $sheet->setCellValue('A' . $rowNumber, $result->participant_name);
            $sheet->setCellValue('B' . $rowNumber, $result->participant_email);
            $sheet->setCellValue('C' . $rowNumber, $result->phone_number);
            $sheet->setCellValue('D' . $rowNumber, $result->education);
            $sheet->setCellValue('E' . $rowNumber, $result->major);
            $sheet->setCellValue('F' . $rowNumber, $result->score);
            $sheet->setCellValue('G' . $rowNumber, $result->created_at->format('d-m-Y H:i'));
            $rowNumber++;
        }

        // Membuat file dan memicu unduhan
        $fileName = 'hasil-' . \Illuminate\Support\Str::slug($test->title) . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        // Menyiapkan header untuk unduhan
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
        
        // Mengirim file ke output browser
        $writer->save('php://output');
        exit();
    }
}