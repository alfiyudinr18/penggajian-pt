<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Slip Gaji</title>

<style>
@page {
    size: A4;
    margin: 10mm;
}

body {
    font-family: Arial, sans-serif;
    font-size: 11px;
}

/* === SATU HALAMAN === */
.page {
    width: 100%;
    page-break-after: always;
}

.page:last-child {
    page-break-after: auto; /* ⬅️ WAJIB */
}

/* === GRID 2x2 PALING AMAN DOMPDF === */
.cell {
    display: inline-block;
    width: 90mm;          /* FIXED */
    height: 125mm;        /* FIXED */
    margin: 2mm;
    vertical-align: top;
    box-sizing: border-box;
}

/* === SLIP === */
.slip {
    border: 1px solid #000;
    padding: 6px;
    height: 100%;
    box-sizing: border-box;
}

.header { text-align: center; font-weight: bold; }
.title { font-size: 14px; }
.line { border-top: 1px solid #000; margin: 4px 0; }

.row {
    clear: both;
}

.left { float: left; width: 65%; }
.right { float: right; width: 35%; text-align: right; }

.blue {
    background: #6ea7d8;
    font-weight: bold;
    text-align: center;
    padding: 2px;
}

.light {
    background: #b9d7f0;
    text-align: center;
    padding: 2px;
}

.total {
    border-top: 1px solid #000;
    margin-top: 6px;
    padding-top: 4px;
    font-weight: bold;
}

.red { color: red; }
</style>
</head>
<body>

@foreach($penggajianList->chunk(4) as $chunk)
<div class="page">

    @foreach($chunk as $p)
        <div class="cell">
            @include('penggajian._slip', ['p' => $p])
        </div>
    @endforeach

</div>
@endforeach

</body>
</html>
