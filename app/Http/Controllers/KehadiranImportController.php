<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\KehadiranImport;
use Maatwebsite\Excel\Facades\Excel;

class KehadiranImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx|max:5120' // Maksimal 5MB
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
