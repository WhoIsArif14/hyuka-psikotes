<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PapiKostickItem; // Pastikan nama Model sudah benar!
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PapiKostickItemController extends Controller
{
    /**
     * Tampilkan daftar semua item PAPI Kostick. (Index)
     */
    public function index()
    {
        $papiItems = PapiKostickItem::orderBy('item_number')->paginate(15);
        
        return view('admin.papi-items.index', compact('papiItems'));
    }

    // --- Create & Store ---

    /**
     * Tampilkan form untuk membuat item PAPI baru. (Create)
     */
    public function create()
    {
        return view('admin.papi-items.create');
    }

    /**
     * Simpan item PAPI baru ke database. (Store)
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_number' => 'required|integer|unique:papi_kostick_items,item_number',
            'statement_a' => 'required|string|max:255',
            'statement_b' => 'required|string|max:255',
            'aspect_a' => 'required|string|max:5',
            'aspect_b' => 'required|string|max:5',
        ]);

        DB::beginTransaction();
        try {
            PapiKostickItem::create($request->all());
            DB::commit();

            return redirect()
                ->route('admin.papi-items.index')
                ->with('success', '✅ Item PAPI berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal menyimpan item PAPI: ' . $e->getMessage());
        }
    }

    // --- Edit & Update ---
    
    /**
     * Tampilkan form untuk mengedit item PAPI. (Edit)
     */
    public function edit(PapiKostickItem $papiItem) // Menggunakan Route Model Binding
    {
        return view('admin.papi-items.edit', compact('papiItem'));
    }

    /**
     * Perbarui item PAPI di database. (Update)
     */
    public function update(Request $request, PapiKostickItem $papiItem)
    {
        $request->validate([
            'item_number' => 'required|integer|unique:papi_kostick_items,item_number,' . $papiItem->id,
            'statement_a' => 'required|string|max:255',
            'statement_b' => 'required|string|max:255',
            'aspect_a' => 'required|string|max:5',
            'aspect_b' => 'required|string|max:5',
        ]);

        DB::beginTransaction();
        try {
            $papiItem->update($request->all());
            DB::commit();

            return redirect()
                ->route('admin.papi-items.index')
                ->with('success', '✅ Item PAPI berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Gagal memperbarui item PAPI: ' . $e->getMessage());
        }
    }

    // --- Delete ---

    /**
     * Hapus item PAPI dari database. (Destroy)
     */
    public function destroy(PapiKostickItem $papiItem)
    {
        try {
            $papiItem->delete();
            return back()->with('success', '🗑️ Item PAPI berhasil dihapus.');
        } catch (\Exception $e) {
            // Tangani error jika item ini digunakan (Constraint Violation)
            return back()->with('error', '❌ Gagal menghapus item PAPI: ' . $e->getMessage());
        }
    }
}