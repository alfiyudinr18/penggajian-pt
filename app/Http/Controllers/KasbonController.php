<?php

namespace App\Http\Controllers;

use App\Models\Kasbon;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class KasbonController extends Controller
{
    public function index(Request $request)
    {
        $query = Kasbon::with('karyawan');

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $kasbon = $query->orderBy('tanggal', 'desc')->paginate(20);
        $karyawanList = Karyawan::where('is_active', true)->orderBy('nama')->get();

        return view('kasbon.index', compact('kasbon', 'karyawanList'));
    }

    public function create()
    {
        $karyawanList = Karyawan::where('is_active', true)->orderBy('nama')->get();
        return view('kasbon.create', compact('karyawanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'jumlah' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        $validated['sisa'] = $validated['jumlah'];
        $validated['status'] = 'aktif';

        Kasbon::create($validated);

        return redirect()->route('kasbon.index')
            ->with('success', 'Data kasbon berhasil ditambahkan.');
    }

    public function show(Kasbon $kasbon)
    {
        $kasbon->load('karyawan');
        return view('kasbon.show', compact('kasbon'));
    }

    public function edit(Kasbon $kasbon)
    {
        $karyawanList = Karyawan::where('is_active', true)->orderBy('nama')->get();
        return view('kasbon.edit', compact('kasbon', 'karyawanList'));
    }

    public function update(Request $request, Kasbon $kasbon)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'jumlah' => 'required|numeric|min:0',
            'sisa' => 'required|numeric|min:0|lte:jumlah',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,lunas',
        ]);

        $kasbon->update($validated);

        return redirect()->route('kasbon.index')
            ->with('success', 'Data kasbon berhasil diperbarui.');
    }

    public function destroy(Kasbon $kasbon)
    {
        $kasbon->delete();

        return redirect()->route('kasbon.index')
            ->with('success', 'Data kasbon berhasil dihapus.');
    }

    public function potong(Request $request, Kasbon $kasbon)
    {
        $validated = $request->validate([
            'jumlah_potongan' => 'required|numeric|min:0|max:' . $kasbon->sisa,
        ]);

        $kasbon->potong($validated['jumlah_potongan']);

        return redirect()->back()
            ->with('success', 'Kasbon berhasil dipotong.');
    }
}
