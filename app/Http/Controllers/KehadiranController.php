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

        // Check if it's Saturday
        $tanggal = Carbon::parse($validated['tanggal']);
        $validated['is_sabtu'] = $tanggal->isSaturday();

        // Check if it's a holiday (tanggal merah)
        // You can customize this logic or add a holidays table
        $validated['is_tanggal_merah'] = $request->boolean('is_tanggal_merah');

        Kehadiran::updateOrCreate(
            [
                'karyawan_id' => $validated['karyawan_id'],
                'tanggal' => $validated['tanggal']
            ],
            $validated
        );

        return redirect()->route('kehadiran.index')
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
            'scan_1' => 'nullable|date_format:H:i',
            'scan_2' => 'nullable|date_format:H:i',
            'scan_3' => 'nullable|date_format:H:i',
            'scan_4' => 'nullable|date_format:H:i',
            'scan_5' => 'nullable|date_format:H:i',
            'scan_6' => 'nullable|date_format:H:i',
        ]);

        // Check if it's Saturday
        $tanggal = Carbon::parse($validated['tanggal']);
        $validated['is_sabtu'] = $tanggal->isSaturday();

        $validated['is_tanggal_merah'] = $request->boolean('is_tanggal_merah');

        $kehadiran->update($validated);

        return redirect()->route('kehadiran.index')
            ->with('success', 'Data kehadiran berhasil diperbarui.');
    }

    public function destroy(Kehadiran $kehadiran)
    {
        $kehadiran->delete();

        return redirect()->route('kehadiran.index')
            ->with('success', 'Data kehadiran berhasil dihapus.');
    }
}
