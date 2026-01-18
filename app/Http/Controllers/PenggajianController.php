<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Services\PenggajianService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PenggajianController extends Controller
{
    protected $penggajianService;

    public function __construct(PenggajianService $penggajianService)
    {
        $this->penggajianService = $penggajianService;
    }

    public function index(Request $request)
    {
        $query = Penggajian::with('karyawan');

        if ($request->filled('periode_mulai')) {
            $query->where('periode_mulai', $request->periode_mulai);
        }

        if ($request->filled('periode_selesai')) {
            $query->where('periode_selesai', $request->periode_selesai);
        }

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        $penggajian = $query->orderBy('periode_selesai', 'desc')
            ->orderBy('karyawan_id')
            ->paginate(50);

        $karyawanList = Karyawan::where('is_active', true)->orderBy('nama')->get();

        // Calculate totals for current page
        $totals = [
            'hari_kerja' => $penggajian->sum('hari_kerja'),
            'premi_full' => $penggajian->sum('premi_full'),
            'jam_lembur_biasa' => $penggajian->sum('jam_lembur_biasa'),
            'lembur_biasa' => $penggajian->sum('lembur_biasa'),
            'jam_lembur_tgl_merah' => $penggajian->sum('jam_lembur_tgl_merah'),
            'lembur_tgl_merah' => $penggajian->sum('lembur_tgl_merah'),
            'potongan_masuk_siang' => $penggajian->sum('potongan_masuk_siang'),
            'kasbon_baru' => $penggajian->sum('kasbon_baru'),
            'potongan_kasbon' => $penggajian->sum('potongan_kasbon'),
            'total_gaji' => $penggajian->sum('total_gaji'),
        ];

        return view('penggajian.index', compact('penggajian', 'karyawanList', 'totals'));
    }

    public function create()
    {
        $karyawanList = Karyawan::where('is_active', true)->orderBy('nama')->get();

        // Suggest default periode (2 weeks back from today)
        $defaultPeriodeSelesai = Carbon::today();
        $defaultPeriodeMulai = $defaultPeriodeSelesai->copy()->subWeeks(2)->addDay();

        return view('penggajian.create', compact('karyawanList', 'defaultPeriodeMulai', 'defaultPeriodeSelesai'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
            'karyawan_ids' => 'required|array|min:1',
            'karyawan_ids.*' => 'exists:karyawan,id',
        ], [
            'karyawan_ids.required' => 'Pilih minimal satu karyawan.',
            'karyawan_ids.min' => 'Pilih minimal satu karyawan.',
        ]);

        $hasil = $this->penggajianService->generatePenggajianMassal(
            $validated['karyawan_ids'],
            $validated['periode_mulai'],
            $validated['periode_selesai']
        );

        $success = collect($hasil)->where('status', 'success')->count();
        $errors = collect($hasil)->where('status', 'error')->count();

        $message = "Penggajian berhasil dibuat untuk {$success} karyawan.";
        if ($errors > 0) {
            $message .= " {$errors} karyawan gagal diproses.";
        }

        return redirect()->route('penggajian.index', [
            'periode_mulai' => $validated['periode_mulai'],
            'periode_selesai' => $validated['periode_selesai']
        ])->with('success', $message);
    }

    public function show(Penggajian $penggajian)
    {
        $penggajian->load('karyawan');
        return view('penggajian.show', compact('penggajian'));
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
        ]);

        $dataGaji = $this->penggajianService->hitungGaji(
            $validated['karyawan_id'],
            $validated['periode_mulai'],
            $validated['periode_selesai']
        );

        $karyawan = Karyawan::find($validated['karyawan_id']);

        return response()->json([
            'success' => true,
            'data' => $dataGaji,
            'karyawan' => $karyawan
        ]);
    }

    public function destroy(Penggajian $penggajian)
    {
        $penggajian->delete();

        return redirect()->route('penggajian.index')
            ->with('success', 'Data penggajian berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $query = Penggajian::with('karyawan');

        if ($request->filled('periode_mulai')) {
            $query->where('periode_mulai', $request->periode_mulai);
        }

        if ($request->filled('periode_selesai')) {
            $query->where('periode_selesai', $request->periode_selesai);
        }

        $penggajian = $query->orderBy('karyawan_id')->get();

        // Export to Excel or PDF
        // You can use libraries like Laravel Excel or DomPDF

        return view('penggajian.export', compact('penggajian'));
    }

    public function slipPreview(Penggajian $penggajian)
    {
        return view('penggajian.slip', [
            'penggajianList' => collect([$penggajian])
        ]);
    }

    public function slipPdf(Request $request)
    {
        $query = Penggajian::with('karyawan');

        if ($request->filled('periode_mulai')) {
            $query->where('periode_mulai', $request->periode_mulai);
        }

        if ($request->filled('periode_selesai')) {
            $query->where('periode_selesai', $request->periode_selesai);
        }

        if ($request->filled('karyawan_id')) {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // ❗ WAJIB get(), JANGAN paginate()
        $penggajianList = $query
            ->orderBy('karyawan_id')
            ->get();

        // DEBUG (hapus setelah benar)
        // dd($penggajianList->count());

        $pdf = Pdf::loadView(
            'penggajian.slip',
            compact('penggajianList')
        )->setPaper('a4', 'portrait');

        return $pdf->stream('slip-gaji.pdf');
    }


}
