<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\Jenjang;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Exports\TestResultsExport;
use Maatwebsite\Excel\Facades\Excel;

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
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();
        $clients = Client::all();
        return view('admin.tests.create', compact('categories', 'jenjangs', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'test_category_id' => 'required|exists:test_categories,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'client_id' => 'nullable|exists:clients,id',
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
        $categories = TestCategory::all();
        $jenjangs = Jenjang::all();
        $clients = Client::all();
        return view('admin.tests.edit', compact('test', 'categories', 'jenjangs', 'clients'));
    }

    public function update(Request $request, Test $test)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'test_category_id' => 'required|exists:test_categories,id',
            'jenjang_id' => 'required|exists:jenjangs,id',
            'client_id' => 'nullable|exists:clients,id',
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

    /**
     * Mengganti method `results` agar menampilkan halaman manajemen kode aktivasi.
     */
    public function results(Test $test)
    {
        // Mengambil semua KODE AKTIVASI untuk tes ini
        $codes = $test->activationCodes()->with('user')->latest()->get();
        
        return view('admin.tests.results', compact('test', 'codes'));
    }

    public function export(Test $test)
    {
        $fileName = 'hasil-' . \Illuminate\Support\Str::slug($test->title) . '.xlsx';
        return Excel::download(new TestResultsExport($test), $fileName);
    }
}

