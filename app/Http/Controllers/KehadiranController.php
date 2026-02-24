<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class KehadiranController extends Controller
{
    public function index(Request $request)
    {
        $query = Kehadiran::with('karyawan');

        // Filter by karyawan
        if ($request->filled('karyawan_id')) {
            if ($request->karyawan_id != 'all') {
                $query->where('karyawan_id', $request->karyawan_id);
            }
        }

        // Filter by date range
        if ($request->filled('tanggal_mulai')) {
            $query->where('tanggal', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->where('tanggal', '<=', $request->tanggal_selesai);
        }

        $kehadiran = $query->orderBy('tanggal', 'desc')->paginate(50);
        $karyawanList = Karyawan::where('is_active', true)->orderBy('nama')->get();

        return view('kehadiran.index', compact('kehadiran', 'karyawanList'));
    }

    public function create()
    {
        $karyawanList = Karyawan::where('is_active', true)->orderBy('nama')->get();
        return view('kehadiran.create', compact('karyawanList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal' => 'required|date',
            'scan_1' => 'nullable|date_format:H:i',
            'scan_2' => 'nullable|date_format:H:i',
            'scan_3' => 'nullable|date_format:H:i',
            'scan_4' => 'nullable|date_format:H:i',
            'scan_5' => 'nullable|date_format:H:i',
            'scan_6' => 'nullable|date_format:H:i',
        ]);

        $tanggal = Carbon::parse($validated['tanggal']);

        // otomatis sabtu
        $validated['is_sabtu'] = $tanggal->isSaturday();

        // tanggal merah HARUS dari checkbox
        $validated['is_tanggal_merah'] = $request->boolean('is_tanggal_merah');

        Kehadiran::updateOrCreate(
            [
                'karyawan_id' => $validated['karyawan_id'],
                'tanggal' => $validated['tanggal']
            ],
            $validated
        );

        $filters = $request->only(['karyawan_id', 'tanggal_mulai', 'tanggal_selesai']);

        return redirect()->route('kehadiran.index', $filters)
            ->with('success', 'Data kehadiran berhasil ditambahkan.');
    }

    public function edit(Kehadiran $kehadiran)
    {
        $karyawanList = Karyawan::where('is_active', true)->orderBy('nama')->get();
        return view('kehadiran.edit', compact('kehadiran', 'karyawanList'));
    }

    public function update(Request $request, Kehadiran $kehadiran)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'tanggal' => 'required|date',
            'scan_1' => 'nullable',
            'scan_2' => 'nullable',
            'scan_3' => 'nullable',
            'scan_4' => 'nullable',
            'scan_5' => 'nullable',
            'scan_6' => 'nullable',
        ]);

        $tanggal = Carbon::parse($validated['tanggal']);

        // otomatis sabtu
        $validated['is_sabtu'] = $tanggal->isSaturday();

        // tanggal merah dari checkbox
        $validated['is_tanggal_merah'] = $request->boolean('is_tanggal_merah');

        $kehadiran->update($validated);

        $filters = $request->only(['karyawan_id', 'tanggal_mulai', 'tanggal_selesai']);

        return redirect()->route('kehadiran.index', $filters)
            ->with('success', 'Data kehadiran berhasil diperbarui.');
    }

    public function show(Kehadiran $kehadiran)
    {
        return redirect()->route('kehadiran.index', request()->all());
    }


    public function destroy(Request $request,Kehadiran $kehadiran)
    {
        $filters = $request->only(['karyawan_id', 'tanggal_mulai', 'tanggal_selesai']);

        return redirect()->route('kehadiran.index', $filters)
            ->with('success', 'Data kehadiran berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:kehadiran,id'
        ]);

        $jumlah = Kehadiran::whereIn('id', $request->ids)->delete();

        $filters = $request->only(['karyawan_id', 'tanggal_mulai', 'tanggal_selesai']);

        return redirect()->route('kehadiran.index', $filters)
            ->with('success', "Berhasil menghapus {$jumlah} data kehadiran.");
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx|max:5120'
        ]);

        // 2. Eksekusi Import menggunakan Class KehadiranImport
        try {
            Excel::import(new KehadiranImport, $request->file('file'));

            // 3. Redirect Sukses
            return redirect()->route('kehadiran.index')
                ->with('success', 'Data kehadiran berhasil diimport!');

        } catch (\Throwable $e) {
            // 4. Handle Error
            return redirect()->back()
                ->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
}
