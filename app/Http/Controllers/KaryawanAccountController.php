<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class KaryawanAccountController extends Controller
{
    /**
     * Tampilkan form pembuatan akun untuk karyawan tertentu.
     */
    public function create(Karyawan $karyawan)
    {
        // Cegah jika karyawan sudah punya akun
        if ($karyawan->user_id) {
            return redirect()->route('karyawan.index')
                ->with('error', 'Karyawan ini sudah memiliki akun.');
        }

        return view('karyawan.account.create', compact('karyawan'));
    }

    /**
     * Proses penyimpanan akun dan assign role 'karyawan'.
     */
    public function store(Request $request, Karyawan $karyawan)
    {
        // Cegah jika karyawan sudah punya akun
        if ($karyawan->user_id) {
            return redirect()->route('karyawan.index')
                ->with('error', 'Karyawan ini sudah memiliki akun.');
        }

        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        DB::transaction(function () use ($request, $karyawan) {
            // 1. Buat User Baru
            $user = User::create([
                'name' => $karyawan->nama, // Nama otomatis ambil dari data karyawan
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Berikan Role 'karyawan' (Spatie)
            $user->assignRole('karyawan');

            // 3. Hubungkan user_id ke tabel karyawan
            $karyawan->update([
                'user_id' => $user->id
            ]);
        });

        return redirect()->route('karyawan.index')
            ->with('success', 'Akun berhasil dibuat untuk karyawan ' . $karyawan->nama);
    }
}
