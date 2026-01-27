<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\Kasbon;
use App\Services\PenggajianService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PenggajianController extends Controller
{
    protected $penggajianService;

    public function __construct(PenggajianService $penggajianService)
    {
        $this->penggajianService = $penggajianService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Penggajian::with('karyawan');

        // ==================================================
        // LOGIKA KHUSUS KARYAWAN
        // ==================================================
        if ($user->hasRole('karyawan')) {
            // Cek apakah user punya relasi ke tabel karyawan
            if (!$user->karyawan) {
                abort(403, 'Akun Anda belum terhubung ke data karyawan.');
            }

            // Karyawan HANYA bisa lihat miliknya DAN yang sudah FINAL
            $query->where('karyawan_id', $user->karyawan->id)
                ->where('status', 'final');
        }
        // ==================================================
        // LOGIKA KHUSUS ADMIN
        // ==================================================
        else {
            // Admin bisa memfilter berdasarkan Karyawan
            if ($request->filled('karyawan_id')) {
                $query->where('karyawan_id', $request->karyawan_id);
            }
        }

        // Filter Tanggal (Berlaku untuk Admin & Karyawan)
        if ($request->filled('periode_mulai')) {
            $query->where('periode_mulai', '>=', $request->periode_mulai);
        }
        if ($request->filled('periode_selesai')) {
            $query->where('periode_selesai', '<=', $request->periode_selesai);
        }

        $penggajian = $query->orderBy('periode_selesai', 'desc')->paginate(50);

        // Kirim list karyawan hanya untuk Admin (untuk filter dropdown)
        $karyawanList = $user->hasRole('admin') ? Karyawan::where('is_active', true)->orderBy('nama')->get() : [];

        // Perhitungan Total
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

    public function edit(Penggajian $penggajian)
    {
        $penggajian->load('karyawan');

        // sisa kasbon real-time dari tabel kasbon
        $sisaKasbonAktif = \App\Models\Kasbon::where('karyawan_id', $penggajian->karyawan_id)
            ->where('status', 'aktif')
            ->sum('sisa');

        return view('penggajian.edit', compact(
            'penggajian',
            'sisaKasbonAktif'
        ));
    }

    public function update(Request $request, Penggajian $penggajian)
    {
        $validated = $request->validate([
            'hari_kerja' => 'required|integer|min:0',
            'premi_full' => 'required|numeric|min:0',
            'bonus_minggu_1' => 'numeric|min:0',
            'bonus_minggu_2' => 'numeric|min:0',
            'uang_makan' => 'numeric|min:0',
            'potongan_masuk_siang' => 'numeric|min:0',
            'potongan_kasbon' => 'numeric|min:0',
        ]);

        // hitung ulang total gaji
        $total =
            $validated['premi_full'] +
            $validated['bonus_minggu_1'] +
            $validated['bonus_minggu_2'] +
            $validated['uang_makan'] +
            $penggajian->lembur_biasa +
            $penggajian->lembur_tgl_merah -
            $validated['potongan_masuk_siang'] -
            $validated['potongan_kasbon'];

        $penggajian->update([
            ...$validated,
            'total_gaji' => $total,
            'sisa_kasbon' => max(
                0,
                $penggajian->sisa_kasbon - $validated['potongan_kasbon']
            ),
        ]);

        return redirect()
            ->route('penggajian.index')
            ->with('success', 'Penggajian berhasil diperbarui.');
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

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:penggajian,id'
        ]);

        $count = 0;

        // Gunakan loop untuk memastikan logic delete per item berjalan (jika ada observer/event)
        // Atau gunakan whereIn untuk performa cepat
        // Kita perlu pastikan hanya yang statusnya 'draft' yang bisa dihapus

        $deleted = Penggajian::whereIn('id', $request->ids)
            ->where('status', 'draft') // Safety: Hanya hapus yang draft
            ->delete();

        if ($deleted > 0) {
            return redirect()->back()->with('success', "Berhasil menghapus {$deleted} data penggajian.");
        } else {
            return redirect()->back()->with('error', 'Tidak ada data yang dihapus (Mungkin status sudah Final).');
        }
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
        $user = auth()->user();
        $query = Penggajian::with('karyawan');

        // =======================================================
        // 1. KEAMANAN: Filter berdasarkan Role
        // =======================================================
        if ($user->hasRole('karyawan')) {
            // Karyawan HANYA boleh cetak miliknya sendiri yang sudah FINAL
            $query->where('karyawan_id', $user->karyawan->id)
                ->where('status', 'final');
        } else {
            // Admin boleh filter by karyawan_id
            if ($request->filled('karyawan_id')) {
                $query->where('karyawan_id', $request->karyawan_id);
            }
        }

        // =======================================================
        // 2. FILTER SPESIFIK (Untuk tombol di dalam tabel)
        // =======================================================
        if ($request->filled('penggajian_id')) {
            $query->where('id', $request->penggajian_id);
        }

        // =======================================================
        // 3. FILTER TANGGAL (Untuk tombol Cetak Semua di atas)
        // =======================================================
        if ($request->filled('periode_mulai')) {
            $query->where('periode_mulai', $request->periode_mulai);
        }
        if ($request->filled('periode_selesai')) {
            $query->where('periode_selesai', $request->periode_selesai);
        }

        $penggajianList = $query->orderBy('karyawan_id')->get();

        // Jika tidak ada data (misal karyawan iseng ganti ID di URL)
        if ($penggajianList->isEmpty()) {
            abort(403, 'Slip gaji tidak ditemukan, atau belum finalisasi.');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'penggajian.slip',
            compact('penggajianList')
        )->setPaper('a4', 'portrait');

        return $pdf->stream('slip-gaji.pdf');
    }

    public function unfinalize(Penggajian $penggajian)
    {
        if ($penggajian->status !== 'final') {
            return back()->with('error', 'Penggajian belum final.');
        }

        DB::transaction(function () use ($penggajian) {

            // =========================
            // KEMBALIKAN KASBON
            // =========================
            $jumlahDikembalikan = $penggajian->potongan_kasbon;

            if ($jumlahDikembalikan > 0) {

                // Ambil kasbon TERBARU dulu (dibalik dari potongan)
                $kasbonList = Kasbon::where('karyawan_id', $penggajian->karyawan_id)
                    ->orderBy('tanggal', 'desc')
                    ->get();

                foreach ($kasbonList as $kasbon) {
                    if ($jumlahDikembalikan <= 0) break;

                    $sisaRuang = $kasbon->jumlah - $kasbon->sisa;
                    if ($sisaRuang <= 0) continue;

                    $kembali = min($sisaRuang, $jumlahDikembalikan);

                    $kasbon->update([
                        'sisa' => $kasbon->sisa + $kembali,
                        'status' => 'aktif'
                    ]);

                    $jumlahDikembalikan -= $kembali;
                }
            }

            // =========================
            // KEMBALIKAN KE DRAFT
            // =========================
            $penggajian->update([
                'status' => 'draft',
                'finalized_at' => null,
            ]);
        });

        return back()->with(
            'success',
            'Penggajian dikembalikan ke DRAFT dan kasbon dipulihkan.'
        );
    }


    public function finalize(Penggajian $penggajian)
    {
        // ❌ Cegah finalisasi ulang
        if ($penggajian->status === 'final') {
            return back()->with('error', 'Penggajian sudah difinalisasi.');
        }

        DB::transaction(function () use ($penggajian) {

            // =========================
            // POTONG KASBON
            // =========================
            $sisaPotong = $penggajian->potongan_kasbon;

            if ($sisaPotong > 0) {
                $kasbonList = Kasbon::where('karyawan_id', $penggajian->karyawan_id)
                    ->where('status', 'aktif')
                    ->orderBy('tanggal')
                    ->get();

                foreach ($kasbonList as $kasbon) {
                    if ($sisaPotong <= 0) break;

                    $potong = min($kasbon->sisa, $sisaPotong);
                    $kasbon->potong($potong);

                    $sisaPotong -= $potong;
                }
            }

            // =========================
            // FINALISASI PENGGAJIAN
            // =========================
            $penggajian->update([
                'status' => 'final',
                'finalized_at' => now(),
            ]);
        });

        return redirect()
            ->route('penggajian.index', request()->query())
            ->with('success', 'Penggajian berhasil difinalisasi & kasbon dipotong.');
    }

}
