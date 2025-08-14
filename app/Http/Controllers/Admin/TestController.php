<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestCategory;
use Illuminate\Http\Request;

// --- TAMBAHKAN USE STATEMENT DI BAWAH INI ---
use App\Models\TestResult;
use App\Exports\TestResultsExport;
use Maatwebsite\Excel\Facades\Excel;
// -------------------------------------------

class TestController extends Controller
{
    /**
     * Menampilkan daftar semua tes.
     */
    public function index()
    {
        $tests = Test::with('category')->latest()->paginate(10);
        return view('admin.tests.index', compact('tests'));
    }

    /**
     * Menampilkan form untuk membuat tes baru.
     */
    public function create()
    {
        $categories = TestCategory::all();
        return view('admin.tests.create', compact('categories'));
    }

    /**
     * Menyimpan tes baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'test_category_id' => 'required|exists:test_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        Test::create($request->all());

        return redirect()->route('admin.tests.index')->with('success', 'Tes baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit tes.
     */
    public function edit(Test $test)
    {
        $categories = TestCategory::all();
        return view('admin.tests.edit', compact('test', 'categories'));
    }

    /**
     * Mengupdate data tes di database.
     */
    public function update(Request $request, Test $test)
    {
        $request->validate([
            'test_category_id' => 'required|exists:test_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            'is_published' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_published'] = $request->has('is_published');

        $test->update($data);

        return redirect()->route('admin.tests.index')->with('success', 'Tes berhasil diperbarui.');
    }

    /**
     * Menghapus tes dari database.
     */
    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.tests.index')->with('success', 'Tes berhasil dihapus.');
    }
    
    // === METHOD BARU UNTUK MELIHAT HASIL TES ===
    /**
     * Menampilkan hasil tes untuk tes tertentu.
     */
    public function results(Test $test)
    {
        $results = $test->testResults()->with('user')->latest()->paginate(15);
        return view('admin.tests.results', compact('test', 'results'));
    }

    // === METHOD BARU UNTUK EKSPOR KE EXCEL ===
    /**
     * Mengekspor hasil tes ke file Excel.
     */
    public function export(Test $test)
    {
        // Membuat nama file yang dinamis, contoh: hasil-tes-logika-dasar.xlsx
        $fileName = 'hasil-' . \Illuminate\Support\Str::slug($test->title) . '.xlsx';
        
        // Memanggil class export yang sudah kita buat dan memulai unduhan
        return Excel::download(new TestResultsExport($test), $fileName);
    }
}