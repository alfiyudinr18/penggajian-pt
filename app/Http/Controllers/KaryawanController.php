<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawan = Karyawan::latest()->paginate(20);
        return view('karyawan.index', compact('karyawan'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pin' => 'required|unique:karyawan,pin',
            'nip' => 'nullable|string|unique:karyawan,nip,',
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'departemen' => 'nullable|string|max:255',
            'kantor' => 'nullable|string|max:255',
            'gaji_per_hari' => 'required|numeric|min:0',
            'bonus_hadir_per_minggu' => 'nullable|numeric|min:0',
            'uang_makan' => 'nullable|numeric|min:0',
            'lembur_biasa_per_jam' => 'nullable|numeric|min:0',
            'lembur_tanggal_merah_per_jam' => 'nullable|numeric|min:0',
        ]);

        Karyawan::create($validated);

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load(['kehadiran' => function($q) {
            $q->orderBy('tanggal', 'desc')->limit(30);
        }, 'kasbonAktif', 'penggajian' => function($q) {
            $q->orderBy('periode_selesai', 'desc')->limit(10);
        }]);

        return view('karyawan.show', compact('karyawan'));
    }

    public function edit(Karyawan $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $request, Karyawan $karyawan)
    {
        $validated = $request->validate([
            'pin' => 'required|unique:karyawan,pin,' . $karyawan->id,
            'nip' => 'nullable|string|unique:karyawan,nip,' . $karyawan->id,
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'departemen' => 'nullable|string|max:255',
            'kantor' => 'nullable|string|max:255',
            'gaji_per_hari' => 'required|numeric|min:0',
            'bonus_hadir_per_minggu' => 'nullable|numeric|min:0',
            'uang_makan' => 'nullable|numeric|min:0',
            'lembur_biasa_per_jam' => 'nullable|numeric|min:0',
            'lembur_tanggal_merah_per_jam' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $karyawan->update($validated);

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Karyawan $karyawan)
    {
        $karyawan->delete();

        return redirect()->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil dihapus.');
    }
}
