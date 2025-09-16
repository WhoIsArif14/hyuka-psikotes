<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Jenjang;
use App\Models\Test;
use App\Models\TestCategory;
use App\Exports\TestResultsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TestController extends Controller
{
    /**
     * Menampilkan daftar semua tes.
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
     * Menampilkan form untuk membuat tes baru.
     */
    public function create()
    {
        $clients = Client::all();
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();
        return view('admin.tests.create', compact('clients', 'categories', 'jenjangs'));
    }

    /**
     * Menyimpan tes baru ke database.
     */
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

    /**
     * Menampilkan form untuk mengedit tes.
     */
    public function edit(Test $test)
    {
        $clients = Client::all();
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();
        return view('admin.tests.edit', compact('test', 'clients', 'categories', 'jenjangs'));
    }

    /**
     * Mengupdate data tes di database.
     */
    public function update(Request $request, Test $test)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'test_category_id' => 'required|exists:test_categories,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'duration_minutes' => 'required|integer|min:1',
            // Validasi unik untuk update, mengabaikan data tes saat ini
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

    /**
     * Menghapus tes dari database.
     */
    public function destroy(Test $test)
    {
        $test->delete();
        return redirect()->route('admin.tests.index')->with('success', 'Tes berhasil dihapus.');
    }

    /**
     * Menampilkan hasil tes untuk tes tertentu.
     */
    public function results(Test $test)
    {
        $results = $test->testResults()->latest()->paginate(15);
        return view('admin.tests.results', compact('test', 'results'));
    }

    /**
     * Mengekspor hasil tes ke file Excel.
     */
    public function export(Test $test)
    {
        $fileName = 'hasil-' . \Illuminate\Support\Str::slug($test->title) . '.xlsx';
        return Excel::download(new TestResultsExport($test), $fileName);
    }
}

