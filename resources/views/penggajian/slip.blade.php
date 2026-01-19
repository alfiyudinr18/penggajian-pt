<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Slip Gaji</title>

<style>
@page {
    size: A4;
    margin: 5mm;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #000;
    margin: 0;
    padding: 0;
}

/* === SATU HALAMAN === */
.page {
    width: 100%;
    page-break-after: always;
}
.page:last-child {
    page-break-after: auto;
}

/* === GRID 2x2 === */
.cell {
    display: inline-block;
    width: 85mm;
    height: 112mm;
    margin: 1mm;
    vertical-align: top;
    box-sizing: border-box;
}

/* === SLIP === */
.slip {
    position: relative;
    border: 2px solid #333;
    padding: 0;
    height: 100%;
    box-sizing: border-box;
    background: #fff;
    overflow: hidden;
}


.content {
    position: relative;
    z-index: 2;
    padding: 7px 10px;
}

/* === HEADER BARU === */
.header {
    width: 100%;
    border-bottom: 3px solid #333;
    padding: 10px 8px 6px 8px; /* ⬅️ bawah diperkecil */
    margin-bottom: 0;        /* ⬅️ HAPUS GAP */
    box-sizing: border-box;
}

.header-top {
    display: table;
    width: 100%;
}

.header-left {
    display: table-cell;
    width: 25%;
    vertical-align: bottom;
    text-align: left;
    padding-right: 8px;
}

.logo {
    width: 55px;
    height: auto;
}

.header-center {
    display: table-cell;
    width: 50%;
    vertical-align: middle;
    text-align: center;
}

.company {
    color: #d4af37;
    font-weight: bold;
    font-size: 13px;
    letter-spacing: 0.5px;
    line-height: 1.2;
    margin-bottom: 2px;
}

.title {
    font-size: 13.5px;      /* ⬅️ lebih besar */
    font-weight: bold;      /* ⬅️ bold */
    margin-top: 2px;
    letter-spacing: 2px;
    text-transform: uppercase;
}

.header-right {
    display: table-cell;
    width: 25%;
    vertical-align: middle;
    text-align: right;
    padding-left: 8px;
}

.slip-no {
    font-size: 10px;
    color: #333;
    border: 1px solid #999;
    padding: 4px 8px;
    display: inline-block;
    background: #fff;
}

.header-logo {
    display: none;
}


/* === TANGGAL === */
.date-row {
    background: #f5f5f5;
    padding: 4px 6px;
    margin: 0 -40mm 6px -40mm;   /* ⬅️ margin-top = 0 */
    padding-left: 40mm;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    font-size: 10px;
}

.date-label {
    display: inline-block;
    width: 50px;
    font-weight: bold;
}

.date-value {
    display: inline-block;
}

/* === BAR NAMA === */
.blue {
    background: #4a90d9;
    color: #ffffff;
    font-weight: bold;
    text-align: center;
    padding: 6px 5px;
    margin: 0 -8px 0 -40mm;
    padding-left: 40mm;

    font-size: 12px;          /* ⬅️ sedikit diperbesar */
    letter-spacing: 0.8px;   /* ⬅️ bikin tajam */
    text-transform: uppercase;

    border-top: 1px solid #2f6fb2;
    border-bottom: 1px solid #2f6fb2;
}

.light {
    background: #eef6ff;
    color: #1e5fa8;
    text-align: center;
    padding: 4px 5px;
    margin: 0 -8px 6px -40mm;
    padding-left: 40mm;

    font-size: 10px;
    font-weight: 700;        /* ⬅️ lebih tegas */
    letter-spacing: 1px;     /* ⬅️ tajam tapi kecil */
    text-transform: uppercase;

    border-bottom: 1px solid #b5d6f2;
}


/* === ROW DATA === */
.row {
    clear: both;
    margin: 2px 0;
    line-height: 1.3;
}

.left {
    float: left;
    width: 63%;
    font-size: 10.5px;
}

.right {
    float: right;
    width: 37%;
    text-align: right;
    font-weight: 600;
    font-size: 10.5px;
}

/* === SEPARATOR === */
.separator {
    border-top: 1px dashed #ccc;
    margin: 4px 0;
}

/* === TOTAL === */
.total {
    border-top: 2.5px double #000;
    margin-top: 6px;
    padding-top: 5px;
    font-weight: bold;
    font-size: 11.5px;
    background: #fffef7;
    padding: 5px 3px 3px 3px;
    margin-left: -3px;
    margin-right: -3px;
}

.total .left,
.total .right {
    font-size: 11.5px;
}

/* === WARNA === */
.red {
    color: #d32f2f;
}

/* === FOOTER === */
.footer {
    margin-top: 10px;
    padding-top: 5px;
    border-top: 1px solid #ddd;
    text-align: center;
    font-size: 10px;
    color: #555;
}

.signature {
    margin-top: 25px;
    text-align: center;
    font-size: 10.5px;
}

.signature-line {
    display: inline-block;
    width: 100px;
    border-bottom: 1px solid #000;
    margin-top: 2px;
}
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
