<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jenjang;
use Illuminate\Http\Request;

class JenjangController extends Controller
{
    public function index()
    {
        $jenjangs = Jenjang::latest()->paginate(10);
        return view('admin.jenjangs.index', compact('jenjangs'));
    }

    public function create()
    {
        return view('admin.jenjangs.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:jenjangs,name']);
        Jenjang::create($request->all());
        return redirect()->route('admin.jenjangs.index')->with('success', 'Jenjang baru berhasil ditambahkan.');
    }

    public function edit(Jenjang $jenjang)
    {
        return view('admin.jenjangs.edit', compact('jenjang'));
    }

    public function update(Request $request, Jenjang $jenjang)
    {
        $request->validate(['name' => 'required|string|max:255|unique:jenjangs,name,' . $jenjang->id]);
        $jenjang->update($request->all());
        return redirect()->route('admin.jenjangs.index')->with('success', 'Jenjang berhasil diperbarui.');
    }

    public function destroy(Jenjang $jenjang)
    {
        $jenjang->delete();
        return redirect()->route('admin.jenjangs.index')->with('success', 'Jenjang berhasil dihapus.');
    }
}