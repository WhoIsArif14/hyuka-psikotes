<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TestCategory;
use Illuminate\Http\Request;

class TestCategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     */
    public function index()
    {
        $categories = TestCategory::latest()->paginate(10); // Ambil 10 kategori per halaman
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Menampilkan form untuk membuat kategori baru.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Menyimpan kategori baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255|unique:test_categories,name',
            'description' => 'nullable|string',
        ]);

        // 2. Buat kategori baru
        TestCategory::create($request->all());

        // 3. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.categories.index')->with('success', 'Kategori baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu kategori (opsional, tidak kita gunakan saat ini).
     */
    public function show(TestCategory $category)
    {
        // Biasanya untuk API atau halaman detail. Bisa dikosongkan untuk saat ini.
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Menampilkan form untuk mengedit kategori.
     * Laravel secara otomatis akan mencari TestCategory berdasarkan ID dari URL.
     */
    public function edit(TestCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Mengupdate data kategori di database.
     */
    public function update(Request $request, TestCategory $category)
    {
        // 1. Validasi input
        $request->validate([
            'name' => 'required|string|max:255|unique:test_categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        // 2. Update kategori
        $category->update($request->all());

        // 3. Redirect kembali dengan pesan sukses
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Menghapus kategori dari database.
     */
    public function destroy(TestCategory $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}