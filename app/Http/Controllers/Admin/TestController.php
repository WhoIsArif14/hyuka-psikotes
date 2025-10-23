<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Client;
use App\Models\Jenjang;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\PapiQuestion; // <-- Diperlukan untuk menghitung soal PAPI
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Mengganti Maatwebsite dengan PhpSpreadsheet untuk ekspor
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TestController extends Controller
{
    /**
     * Menampilkan daftar semua Modul Tes (dengan hitungan soal PAPI).
     */
    public function index()
    {
        $tests = Test::with(['category', 'jenjang', 'client', 'AlatTes']) // Muat AlatTes untuk cek PAPI
            ->withCount('questions')
            ->latest()
            ->paginate(10);
            
        // Logika untuk menghitung soal total (termasuk 90 soal PAPI jika ada)
        $papiQuestionCount = PapiQuestion::count();

        foreach ($tests as $test) {
            $hasPapi = $test->AlatTes->pluck('slug')->contains('papi-kostick');
            
            if ($hasPapi) {
                // Total soal = soal umum + 90 soal PAPI
                $test->total_questions = $test->questions_count + $papiQuestionCount; 
            } else {
                $test->total_questions = $test->questions_count;
            }
        }
            
        return view('admin.tests.index', compact('tests'));
    }

    /**
     * Menampilkan form untuk membuat Modul Tes baru.
     */
    public function create()
    {
        $clients = Client::all();
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();

        // Ambil semua Alat Tes untuk multiple select
        $AlatTes = AlatTes::all(['id', 'name', 'duration_minutes', 'slug']); // Ambil slug

        // Daftar Tipe Data Diri yang bisa dipilih Admin
        $dataTypes = [
            'DATA_DIRI' => 'DATA DIRI',
            'DATA_SEKOLAH' => 'DATA DIRI SEKOLAH',
            'BIODATA_REKRUTMEN' => 'BIODATA REKRUITMEN PEGAWAI',
            'BIODATA_MAPPING' => 'BIODATA MAPPING PEGAWAI',
            'BIO_FLK' => 'Bio Data/FLK',
        ];

        return view('admin.tests.create', compact('clients', 'categories', 'jenjangs', 'AlatTes', 'dataTypes'));
    }

    /**
     * Menyimpan Modul Tes baru ke database (dengan Multiple Alat Tes).
     */
    public function store(Request $request)
    {
        // 1. Validasi Data
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'test_category_id' => 'required|exists:test_categories,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_data_type' => 'required|string|max:50',
            'duration_minutes' => 'required|integer|min:1',
            'alat_tes_ids' => 'required|array',
            'alat_tes_ids.*' => 'exists:alat_tes,id',
            'test_code' => 'nullable|string|max:8|unique:tests,test_code',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
        ]);

        $data = $request->except('alat_tes_ids');
        $data['is_published'] = $request->has('is_published');
        $data['is_template'] = $request->has('is_template');

        $AlatTesIds = $request->input('alat_tes_ids');

        // 2. Gunakan Transaction
        DB::beginTransaction();

        try {
            $test = Test::create($data);
            $test->AlatTes()->attach($AlatTesIds); // Hubungkan ke Alat Tes

            DB::commit();

            return redirect()->route('admin.tests.index')->with('success', 'Tes baru berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan Tes. Error: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan form edit Modul Tes.
     */
    public function edit(Test $test)
    {
        $clients = Client::all();
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();

        // Ambil Alat Tes yang tersedia dan yang sudah terpilih
        $AlatTes = AlatTes::all(['id', 'name', 'duration_minutes', 'slug']);
        $selectedAlatTes = $test->AlatTes->pluck('id')->toArray();

        $dataTypes = [
            'DATA_DIRI' => 'DATA DIRI',
            'DATA_SEKOLAH' => 'DATA DIRI SEKOLAH',
            'BIODATA_REKRUTMEN' => 'BIODATA REKRUITMEN PEGAWAI',
            'BIODATA_MAPPING' => 'BIODATA MAPPING PEGAWAI',
            'BIO_FLK' => 'Bio Data/FLK',
        ];

        return view('admin.tests.edit', compact('test', 'clients', 'categories', 'jenjangs', 'AlatTes', 'selectedAlatTes', 'dataTypes'));
    }

    /**
     * Mengupdate Modul Tes di database.
     */
    public function update(Request $request, Test $test)
    {
        // 1. Validasi Data
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'test_category_id' => 'required|exists:test_categories,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'required_data_type' => 'required|string|max:50',
            'duration_minutes' => 'required|integer|min:1',
            'alat_tes_ids' => 'required|array',
            'alat_tes_ids.*' => 'exists:alat_tes,id',
            'test_code' => 'nullable|string|max:8|unique:tests,test_code,' . $test->id,
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
        ]);

        $data = $request->except('alat_tes_ids');
        $data['is_published'] = $request->has('is_published');
        $data['is_template'] = $request->has('is_template');

        $AlatTesIds = $request->input('alat_tes_ids');

        // 2. Gunakan Transaction
        DB::beginTransaction();

        try {
            $test->update($data);
            $test->AlatTes()->sync($AlatTesIds); // Sinkronisasi Alat Tes

            DB::commit();

            return redirect()->route('admin.tests.index')->with('success', 'Tes berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui Tes. Error: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus Modul Tes dari database.
     */
    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.tests.index')->with('success', 'Tes berhasil dihapus.');
    }

    /**
     * Menampilkan hasil tes.
     */
    public function results(Test $test)
    {
        // Catatan: Jika Modul Tes ini mengandung PAPI, Anda mungkin perlu logic 
        // khusus untuk menampilkan hasil PAPI di sini.
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
        $fileName = 'hasil-' . Str::slug($test->title) . '.xlsx';
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