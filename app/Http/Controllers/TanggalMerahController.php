<?php

namespace App\Http\Controllers;

use App\Models\TanggalMerah;
use Illuminate\Http\Request;

class TanggalMerahController extends Controller
{
    public function index()
    {
        $data = TanggalMerah::orderBy('tanggal', 'desc')->paginate(15);
        return view('tanggal_merah.index', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:tanggal_merah,tanggal',
            'keterangan' => 'nullable|string|max:255'
        ]);

        TanggalMerah::create($request->all());

        return back()->with('success', 'Tanggal merah berhasil ditambahkan');
    }

    public function update(Request $request, TanggalMerah $tanggal_merah)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:tanggal_merah,tanggal,' . $tanggal_merah->id,
            'keterangan' => 'nullable|string|max:255'
        ]);

        $tanggal_merah->update($request->all());

        return back()->with('success', 'Tanggal merah berhasil diperbarui');
    }

    public function destroy(TanggalMerah $tanggal_merah)
    {
        $tanggal_merah->delete();
        return back()->with('success', 'Tanggal merah dihapus');
    }
}
