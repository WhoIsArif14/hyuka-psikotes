<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\Client;
use App\Models\Jenjang;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\PapiQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session; // <-- TAMBAHKAN INI

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
        $tests = Test::with(['category', 'jenjang', 'client', 'AlatTes'])
            ->withCount('questions')
            ->latest()
            ->paginate(10);
            
        // Logika untuk menghitung soal total (termasuk 90 soal PAPI jika ada)
        $papiQuestionCount = PapiQuestion::count();

        foreach ($tests as $test) {
            // Catatan: Pastikan AlatTes memiliki kolom 'slug'
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

// -----------------------------------------------------------------------
// MULTI-STEP CREATE LOGIC (MENGGANTIKAN create() dan store() lama)
// -----------------------------------------------------------------------

    /**
     * TAHAP 1: Menampilkan form untuk membuat Modul Tes baru (Informasi Dasar & Pilih Alat Tes).
     */
    public function create()
    {
        $clients = Client::all();
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();
        $AlatTes = AlatTes::all(['id', 'name', 'duration_minutes', 'slug']);

        $dataTypes = [
            'DATA_DIRI' => 'DATA DIRI',
            'DATA_SEKOLAH' => 'DATA DIRI SEKOLAH',
            'BIODATA_REKRUTMEN' => 'BIODATA REKRUITMEN PEGAWAI',
            'BIODATA_MAPPING' => 'BIODATA MAPPING PEGAWAI',
            'BIO_FLK' => 'Bio Data/FLK',
        ];

        // Jika ada data di session (misal kembali dari step 2), muat data tersebut
        $tempData = Session::get('temp_test_data');

        return view('admin.tests.create-step-one', compact('clients', 'categories', 'jenjangs', 'AlatTes', 'dataTypes', 'tempData'));
    }

    /**
     * TAHAP 1.5: Menyimpan data Tahap 1 ke Session dan mengarahkan ke Tahap 2.
     */
    public function storeStepOne(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'required_data_type' => 'required|string',
            'client_id' => 'nullable|exists:clients,id',
            'test_category_id' => 'required|exists:test_categories,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'duration_minutes' => 'required|integer|min:1', // Min 1 karena ada alat tes yang dipilih
            'test_code' => 'nullable|string|max:8|unique:tests,test_code',
            'alat_tes_ids' => 'required|array|min:1',
            'alat_tes_ids.*' => 'exists:alat_tes,id',
            'description' => 'required|string',
            'available_from' => 'nullable|date',
            'available_to' => 'nullable|date|after_or_equal:available_from',
            'is_published' => 'nullable', // Diterima sebagai checkbox
            'is_template' => 'nullable', // Diterima sebagai checkbox
        ]);

        // Tangani checkbox (karena 'nullable' bisa jadi tidak ada di request)
        $validatedData['is_published'] = $request->has('is_published');
        $validatedData['is_template'] = $request->has('is_template');
        
        // Simpan semua data ke session untuk digunakan di tahap berikutnya
        Session::put('temp_test_data', $validatedData);

        // Ambil data Alat Tes yang dipilih untuk ditampilkan di tahap 2
        $alatTesList = AlatTes::whereIn('id', $validatedData['alat_tes_ids'])
                                ->get(); 

        Session::put('temp_alat_tes_list', $alatTesList);

        return redirect()->route('admin.tests.create.order');
    }

    /**
     * TAHAP 2: Menampilkan form untuk mengatur urutan Alat Tes.
     */
    public function createOrder()
    {
        $tempData = Session::get('temp_test_data');
        $alatTesList = Session::get('temp_alat_tes_list');

        if (!$tempData || !$alatTesList) {
            return redirect()->route('admin.tests.create')->with('error', 'Data modul tidak ditemukan. Harap ulangi langkah 1.');
        }

        return view('admin.tests.create-step-two', [
            'tempData' => $tempData,
            'alatTesList' => $alatTesList,
        ]);
    }

    /**
     * TAHAP 2.5: Menyimpan data dari Session + Urutan Alat Tes ke Database (Final Store).
     */
    public function storeFinal(Request $request)
    {
        // Ambil data dari session
        $tempData = Session::get('temp_test_data');
        $alatTesList = Session::get('temp_alat_tes_list');

        if (!$tempData || !$alatTesList) {
            return redirect()->route('admin.tests.create')->with('error', 'Sesi data tidak valid. Harap ulangi proses pembuatan modul.');
        }

        $request->validate([
            'test_order' => 'required|json', // Data urutan dari drag & drop
        ]);

        $testOrder = json_decode($request->input('test_order'), true);
        
        // Siapkan data untuk Mass Assignment ke model Test
        // HANYA ambil field yang ada di $fillable model Test
        $dataToStore = [
            'title' => $tempData['title'],
            'required_data_type' => $tempData['required_data_type'],
            'client_id' => $tempData['client_id'],
            'test_category_id' => $tempData['test_category_id'],
            'jenjang_id' => $tempData['jenjang_id'],
            'duration_minutes' => $tempData['duration_minutes'],
            'test_code' => $tempData['test_code'],
            'description' => $tempData['description'],
            'available_from' => $tempData['available_from'] ?? null,
            'available_to' => $tempData['available_to'] ?? null,
            'is_published' => $tempData['is_published'] ?? false,
            'is_template' => $tempData['is_template'] ?? false,
            'test_order' => json_encode($testOrder), // Urutan Alat Tes (JSON string)
        ];

        DB::beginTransaction();

        try {
            // 1. Buat Test baru
            $test = Test::create($dataToStore);

            // 2. Sinkronisasi Alat Tes menggunakan IDs dari $tempData
            $test->AlatTes()->sync($tempData['alat_tes_ids']);

            DB::commit();

            // 3. Hapus data session
            Session::forget(['temp_test_data', 'temp_alat_tes_list']);

            return redirect()->route('admin.tests.index')
                ->with('success', 'Modul tes "' . $test->title . '" berhasil dibuat dan urutan telah disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus data session agar user bisa mengulangi dari awal jika ada error fatal
            Session::forget(['temp_test_data', 'temp_alat_tes_list']);
            return redirect()->route('admin.tests.create')->with('error', 'Gagal menyimpan Tes. Harap ulangi proses pembuatan. Error: ' . $e->getMessage());
        }
    }

    /**
     * MENGGANTIKAN METHOD store() LAMA. Method ini hanya berfungsi sebagai fallback/error.
     */
    public function store(Request $request)
    {
        // Redirect ke alur multi-step jika user mencoba POST ke rute lama
        return redirect()->route('admin.tests.create')->with('error', 'Gunakan alur multi-step untuk membuat tes baru (Langkah 1 & Langkah 2).');
    }

// -----------------------------------------------------------------------
// END MULTI-STEP CREATE LOGIC
// -----------------------------------------------------------------------

    /**
     * Menampilkan form edit Modul Tes.
     */
    public function edit(Test $test)
    {
        $clients = Client::all();
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();

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
            'test_order' => 'nullable|json', // Kolom ini bisa dikirim dari form edit jika Anda mengaturnya di form
        ]);

        $data = $request->except('alat_tes_ids');
        $data['is_published'] = $request->has('is_published');
        $data['is_template'] = $request->has('is_template');
        
        // Pastikan test_order diupdate jika dikirim, atau pertahankan nilai lama jika tidak di form
        if($request->filled('test_order')){
            $data['test_order'] = $request->input('test_order');
        } else {
            // Jika form edit Anda tidak mengelola urutan, gunakan nilai lama (jika ada)
            unset($data['test_order']);
        }


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
    
    // -----------------------------------------------------------------------
    // Tambahan untuk Mengelola Urutan (Opsional pada Modul Edit)
    // -----------------------------------------------------------------------

    /**
     * Menampilkan halaman edit urutan Alat Tes untuk Test yang SUDAH ADA.
     */
    public function editOrder(Test $test)
    {
        // Ambil Alat Tes yang terkait
        $alatTesList = $test->AlatTes; 
        
        // Urutkan Alat Tes berdasarkan kolom 'test_order' jika sudah ada
        if ($test->test_order) {
            $orderedIds = $test->test_order; // array of IDs
            // Urutkan AlatTes menggunakan urutan yang tersimpan
            $alatTesList = $alatTesList->sortBy(function($item) use ($orderedIds) {
                return array_search($item->id, $orderedIds);
            })->values();
        }
        
        return view('admin.tests.edit-order', compact('test', 'alatTesList'));
    }

    /**
     * Mengupdate urutan Alat Tes untuk Test yang SUDAH ADA.
     */
    public function updateOrder(Request $request, Test $test)
    {
        $request->validate([
            'test_order' => 'required|json',
        ]);
        
        $testOrder = json_decode($request->input('test_order'), true);
        
        try {
            $test->update([
                'test_order' => $testOrder
            ]);
            
            return redirect()->route('admin.tests.index')->with('success', 'Urutan Alat Tes untuk "' . $test->title . '" berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui urutan. Error: ' . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------
    // END Tambahan untuk Mengelola Urutan
    // -----------------------------------------------------------------------

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
    public function show(Test $test)
    {
        // Asumsi method show() Anda digunakan untuk melihat detail/hasil tes
        $results = $test->testResults()->latest()->paginate(15);
        
        // Pastikan Anda memiliki view 'admin.tests.show'
        return view('admin.tests.show', compact('test', 'results')); 
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