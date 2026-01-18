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
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        Excel::import(new KehadiranImport, $request->file('file'));

        return back()->with('success', 'Data kehadiran berhasil diimport');
    }
}
