<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Client;
use App\Models\Jenjang;
use App\Models\Test;
use App\Models\TestCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Digunakan untuk Transaction
use Illuminate\Support\Str;

// Mengganti Maatwebsite dengan PhpSpreadsheet untuk ekspor
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TestController extends Controller
{
    /**
     * Menampilkan daftar semua Modul Tes.
     */
    public function index()
    {
        $tests = Test::with(['category', 'jenjang', 'client'])
                     ->withCount('questions')
                     ->latest()
                     ->paginate(10);
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
        
        // Ambil semua Alat Tes untuk multiple select (dengan durasi)
        $alatTes = AlatTes::all(['id', 'name', 'duration_minutes']);

        // Daftar Tipe Data Diri yang bisa dipilih Admin
        $dataTypes = [
            'DATA_DIRI' => 'DATA DIRI',
            'DATA_SEKOLAH' => 'DATA DIRI SEKOLAH',
            'BIODATA_REKRUTMEN' => 'BIODATA REKRUITMEN PEGAWAI',
            'BIODATA_MAPPING' => 'BIODATA MAPPING PEGAWAI',
            'BIO_FLK' => 'Bio Data/FLK',
        ];

        // Kirim semua data ke view
        return view('admin.tests.create', compact('clients', 'categories', 'jenjangs', 'alatTes', 'dataTypes'));
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
            
            // BARU: Tipe data diri yang wajib diisi
            'required_data_type' => 'required|string|max:50',
            
            // Durasi dan Alat Tes yang dipilih
            'duration_minutes' => 'required|integer|min:1',
            'alat_tes_ids' => 'required|array',
            'alat_tes_ids.*' => 'exists:alat_tes,id',
            
            'test_code' => 'nullable|string|max:8|unique:tests,test_code',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
        ]);

        $data = $request->except('alat_tes_ids'); // Ambil semua kecuali ID Alat Tes
        $data['is_published'] = $request->has('is_published');
        $data['is_template'] = $request->has('is_template');
        
        $alatTesIds = $request->input('alat_tes_ids');

        // 2. Gunakan Transaction untuk memastikan Modul dan relasi tersimpan aman
        DB::beginTransaction();

        try {
            // A. Buat Test (Modul)
            $test = Test::create($data);

            // B. Hubungkan ke Alat Tes (tabel pivot)
            $test->alatTes()->attach($alatTesIds);

            DB::commit();

            return redirect()->route('admin.tests.index')->with('success', 'Tes baru berhasil ditambahkan dan Alat Tes terkait telah disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Tampilkan error ke pengguna jika gagal
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan Tes dan Alat Tes terkait. Silakan coba lagi. Error: ' . $e->getMessage());
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
        $alatTes = AlatTes::all(['id', 'name', 'duration_minutes']);
        $selectedAlatTes = $test->alatTes->pluck('id')->toArray();

        // Daftar Tipe Data Diri (Sama seperti create)
        $dataTypes = [
            'DATA_DIRI' => 'DATA DIRI',
            'DATA_SEKOLAH' => 'DATA DIRI SEKOLAH',
            'BIODATA_REKRUTMEN' => 'BIODATA REKRUITMEN PEGAWAI',
            'BIODATA_MAPPING' => 'BIODATA MAPPING PEGAWAI',
            'BIO_FLK' => 'Bio Data/FLK',
        ];
        
        // Kirim semua data ke view
        return view('admin.tests.edit', compact('test', 'clients', 'categories', 'jenjangs', 'alatTes', 'selectedAlatTes', 'dataTypes'));
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

            // BARU: Tipe data diri yang wajib diisi
            'required_data_type' => 'required|string|max:50',
            
            // Validasi untuk Durasi dan Alat Tes yang dipilih
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
        
        $alatTesIds = $request->input('alat_tes_ids');

        // 2. Gunakan Transaction untuk atomicity
        DB::beginTransaction();

        try {
            // A. Update Test (Modul)
            $test->update($data);

            // B. Sinkronisasi Alat Tes (menghapus yang lama dan menambahkan yang baru)
            $test->alatTes()->sync($alatTesIds);

            DB::commit();

            return redirect()->route('admin.tests.index')->with('success', 'Tes berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui Tes dan Alat Tes terkait. Silakan coba lagi. Error: ' . $e->getMessage());
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
