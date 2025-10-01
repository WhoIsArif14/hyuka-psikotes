<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlatTes;
use App\Models\MemoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemoryItemController extends Controller
{
    /**
     * Menampilkan daftar item memori untuk Alat Tes tertentu.
     */
    public function index(AlatTes $alatTe)
    {
        $items = $alatTe->memoryItems()->orderBy('order')->paginate(10);
        return view('admin.memory-items.index', compact('items', 'alatTe'));
    }

    /**
     * Menampilkan form untuk membuat item memori baru.
     */
    public function create(AlatTes $alatTe)
    {
        return view('admin.memory-items.create', compact('alatTe'));
    }

    /**
     * Menyimpan Item Memori baru ke database.
     */
    public function store(Request $request, AlatTes $alatTe)
    {
        $validated = $request->validate([
            'content' => 'required_if:type,TEXT|string|nullable',
            'image_file' => 'required_if:type,IMAGE|image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
            'type' => 'required|in:TEXT,IMAGE',
            'duration_seconds' => 'required|integer|min:1',
            'order' => 'required|integer|min:1',
        ]);
        
        // Handle file upload jika tipe = IMAGE
        if ($validated['type'] === MemoryItem::TYPE_IMAGE && $request->hasFile('image_file')) {
            $validated['content'] = $request->file('image_file')->store('memory_images', 'public');
        }

        $alatTe->memoryItems()->create($validated);

        return redirect()->route('admin.alat-tes.memory-items.index', $alatTe)
                         ->with('success', 'Item memori berhasil ditambahkan.');
    }
    
    // ... Tambahkan metode show, edit, update, dan destroy sesuai kebutuhan
}
