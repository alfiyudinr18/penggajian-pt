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
    font-family: Arial, Helvetica, sans-serif;
    font-size: 11px;
    color: #000;
}

/* === SATU HALAMAN (JANGAN DIUBAH STRUKTURNYA) === */
.page {
    width: 100%;
    page-break-after: always;
}
.page:last-child {
    page-break-after: auto;
}

/* === GRID 2x2 (AMAN DOMPDF) === */
.cell {
    display: inline-block;
    width: 90mm;
    height: 125mm;
    margin: 2mm;
    vertical-align: top;
    box-sizing: border-box;
}

/* === SLIP === */
.slip {
    position: relative;
    border: 1px solid #222;
    padding: 8px;
    height: 100%;
    box-sizing: border-box;
}

/* === WATERMARK === */
.watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 85mm;
    transform: translate(-50%, -50%);
    opacity: 0.06;
    z-index: 0;
}

.content {
    position: relative;
    z-index: 2;
}

/* === HEADER === */
.header {
    text-align: center;
    margin-bottom: 4px;
}

.company {
    color: #c9a24d; /* emas */
    font-weight: bold;
    font-size: 12px;
}

.title {
    font-size: 15px;
    font-weight: bold;
    letter-spacing: 1px;
}

.slip-no {
    position: absolute;
    top: 6px;
    right: 8px;
    font-size: 9px;
    color: #555;
}

/* === GARIS === */
.line {
    border-top: 1px solid #000;
    margin: 4px 0;
}

/* === BAR NAMA === */
.blue {
    background: #6ea7d8;
    color: #000;
    font-weight: bold;
    text-align: center;
    padding: 4px;
}

.light {
    background: #d9ecfb;
    text-align: center;
    padding: 3px;
    font-size: 10px;
}

/* === ROW DATA === */
.row {
    clear: both;
    margin: 2px 0;
}

.left {
    float: left;
    width: 65%;
}

.right {
    float: right;
    width: 35%;
    text-align: right;
}

/* === TOTAL === */
.total {
    border-top: 2px solid #000;
    margin-top: 6px;
    padding-top: 4px;
    font-weight: bold;
}

/* === WARNA === */
.red {
    color: #c00000;
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
