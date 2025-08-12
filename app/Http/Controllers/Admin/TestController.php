<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Test;
use App\Models\TestCategory;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Menampilkan daftar semua tes.
     */
    public function index()
    {
        // Ambil data tes dengan relasi kategori, urutkan dari terbaru, dan paginasi
        $tests = Test::with('category')->latest()->paginate(10);
        return view('admin.tests.index', compact('tests'));
    }

    /**
     * Menampilkan form untuk membuat tes baru.
     */
    public function create()
    {
        // Ambil semua kategori untuk ditampilkan di dropdown
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

        // Siapkan data update, termasuk menangani checkbox
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
}