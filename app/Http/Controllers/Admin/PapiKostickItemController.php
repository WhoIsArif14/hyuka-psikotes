<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PapiKostickItem;

class PapiKostickItemController extends Controller
{
    // Asumsi ada Middleware 'admin' yang melindungi Controller ini
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Menampilkan daftar semua soal
     */
    public function index()
    {
        $items = PapiKostickItem::orderBy('item_number')->paginate(20);
        return view('papi.admin.items.index', compact('items'));
    }

    /**
     * Menampilkan formulir tambah soal
     */
    public function create()
    {
        // Anda mungkin tidak butuh ini jika menggunakan Seeder/Import
        // Tapi jika diperlukan untuk editing manual:
        return view('papi.admin.items.create');
    }

    /**
     * Menyimpan data soal baru
     */
    public function store(Request $request)
    {
        // Logika validasi dan penyimpanan soal
        $request->validate([
            'item_number' => 'required|integer|unique:papi_kostick_items',
            'statement_a' => 'required|string',
            'aspect_a' => 'required|string|max:3', // e.g., 'G', 'W'
            'statement_b' => 'required|string',
            'aspect_b' => 'required|string|max:3',
        ]);

        PapiKostickItem::create($request->all());

        return redirect()->route('admin.papi.items.index')->with('success', 'Soal PAPI berhasil ditambahkan.');
    }
    
    // ... Method edit, update, dan destroy (CRUD standar)
}