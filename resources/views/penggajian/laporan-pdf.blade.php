<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penggajian</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 3px;
        }

        .summary-box {
            margin-bottom: 15px;
        }

        .summary-box table {
            width: 100%;
            border-collapse: collapse;
        }

        .summary-box th, .summary-box td {
            border: 1px solid #000;
            padding: 4px;
        }

        table.detail {
            width: 100%;
            border-collapse: collapse;
        }

        table.detail th,
        table.detail td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        table.detail th {
            background-color: #f2f2f2;
        }

        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .total-row {
            font-weight: bold;
            background-color: #ffffcc;
        }
    </style>
</head>
<body>

<h2>LAPORAN PENGGAJIAN KARYAWAN</h2>
<div class="subtitle">
    Periode:
    <strong>
        {{ \Carbon\Carbon::parse(request('periode_mulai'))->format('d F Y') ?? '-' }}
        s/d
        {{ \Carbon\Carbon::parse(request('periode_selesai'))->format('d F Y') ?? '-' }}
    </strong>
</div>

<!-- Informasi Cetak -->
<table class="info-table">
    <tr>
        <td width="20%"><strong>Tanggal Cetak</strong></td>
        <td width="2%">:</td>
        <td>{{ now()->format('d F Y H:i') }}</td>
    </tr>
    @if(request('karyawan_id'))
    <tr>
        <td><strong>Karyawan</strong></td>
        <td>:</td>
        <td>{{ $penggajianList->first()->karyawan->nama }}</td>
    </tr>
    @endif
</table>

<!-- Ringkasan Total -->
<div class="summary-box">
    <table>
        <tr>
            <th>Total Hari Kerja</th>
            <th>Total Lembur Biasa</th>
            <th>Total Lembur Tgl Merah</th>
            <th>Total Potongan</th>
            <th>Total Gaji Dibayarkan</th>
        </tr>
        <tr>
            <td>{{ $totals['hari_kerja'] }}</td>
            <td>{{ number_format($totals['lembur_biasa'], 0) }}</td>
            <td>{{ number_format($totals['lembur_tgl_merah'], 0) }}</td>
            <td>
                {{ number_format(
                    $totals['potongan_masuk_siang'] + $totals['potongan_kasbon'], 0
                ) }}
            </td>
            <td><strong>{{ number_format($totals['total_gaji'], 0) }}</strong></td>
        </tr>
    </table>
</div>

<!-- Detail Tabel -->
<table class="detail">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Periode</th>
            <th>Hari</th>
            <th>Full</th>
            <th>Bonus</th>
            <th>Lembur</th>
            <th>Potongan</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($penggajianList as $index => $p)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td class="text-left">{{ $p->karyawan->nama }}</td>
            <td>
                {{ \Carbon\Carbon::parse($p->periode_mulai)->format('d/m/Y') }}
                -
                {{ \Carbon\Carbon::parse($p->periode_selesai)->format('d/m/Y') }}
            </td>
            <td>{{ $p->hari_kerja }}</td>
            <td class="text-right">{{ number_format($p->premi_full, 0) }}</td>
            <td class="text-right">
                {{ number_format($p->bonus_minggu_1 + $p->bonus_minggu_2, 0) }}
            </td>
            <td class="text-right">
                {{ number_format($p->lembur_biasa + $p->lembur_tgl_merah, 0) }}
            </td>
            <td class="text-right">
                -{{ number_format($p->potongan_masuk_siang + $p->potongan_kasbon, 0) }}
            </td>
            <td class="text-right"><strong>{{ number_format($p->total_gaji, 0) }}</strong></td>
        </tr>
        @endforeach

        <tr class="total-row">
            <td colspan="8">TOTAL KESELURUHAN</td>
            <td class="text-right">{{ number_format($totals['total_gaji'], 0) }}</td>
        </tr>
    </tbody>
</table>

</body>
</html>
