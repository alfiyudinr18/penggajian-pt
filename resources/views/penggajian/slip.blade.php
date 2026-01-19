<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Slip Gaji</title>

<style>
@page {
    size: A4;
    margin: 8mm;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
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
    width: 92mm;
    height: 130mm;
    margin: 1.5mm;
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

/* === WATERMARK === */
.watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 70mm;
    height: 70mm;
    transform: translate(-50%, -50%) rotate(-15deg);
    opacity: 0.08;
    z-index: 0;
    object-fit: contain;
}

/* Jika logo tidak ada, tampilkan logo default SVG */
.watermark-svg {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 70mm;
    height: 70mm;
    transform: translate(-50%, -50%) rotate(-15deg);
    opacity: 1;
    z-index: 0;
}

.watermark-svg svg {
    width: 100%;
    height: 100%;
    opacity: 0.08;
}

.watermark-default {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 65mm;
    height: 65mm;
    transform: translate(-50%, -50%) rotate(-15deg);
    opacity: 0.05;
    z-index: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.watermark-default svg {
    width: 100%;
    height: 100%;
}

.watermark-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-15deg);
    font-size: 48px;
    font-weight: bold;
    color: #d4d4d4;
    opacity: 0.15;
    z-index: 0;
    text-align: center;
    line-height: 1.2;
    white-space: nowrap;
}

.content {
    position: relative;
    z-index: 2;
    padding: 7px 10px;
}

/* === HEADER === */
.header {
    text-align: center;
    margin-bottom: 12px;
    border-bottom: 3px solid #333;
    padding-bottom: 8px;
    padding-top: 5px;
    background: linear-gradient(to bottom, #fafafa 0%, #ffffff 100%);
    margin-left: -10px;
    margin-right: -10px;
    margin-top: -7px;
    padding-left: 10px;
    padding-right: 10px;
    position: relative;
}

.company {
    color: #b8860b;
    font-weight: bold;
    font-size: 11.5px;
    letter-spacing: 1.2px;
    margin-bottom: 6px;
    text-transform: uppercase;
    line-height: 1.2;
}

.title {
    font-size: 18px;
    font-weight: bold;
    letter-spacing: 4px;
    color: #1a1a1a;
    margin-top: 2px;
    margin-bottom: 5px;
}

.slip-no {
    font-size: 8.5px;
    color: #666;
    background: #f5f5f5;
    padding: 2px 7px;
    border-radius: 3px;
    border: 1px solid #ddd;
    font-weight: 600;
    display: inline-block;
    margin-top: 3px;
}

/* === TANGGAL === */
.date-row {
    background: #f5f5f5;
    padding: 4px 8px;
    margin: 6px -10px 8px -10px;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    font-size: 9px;
}

.date-label {
    display: inline-block;
    width: 55px;
    font-weight: bold;
}

.date-value {
    display: inline-block;
}

/* === BAR NAMA === */
.blue {
    background: #4a90d9;
    color: #fff;
    font-weight: bold;
    text-align: center;
    padding: 6px;
    margin: 0 -10px 0 -10px;
    font-size: 11px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.light {
    background: #e3f2fd;
    color: #1565c0;
    text-align: center;
    padding: 4px;
    margin: 0 -10px 8px -10px;
    font-size: 9px;
    font-weight: 600;
    text-transform: uppercase;
    border-bottom: 1px solid #90caf9;
}

/* === ROW DATA === */
.row {
    clear: both;
    margin: 3px 0;
    line-height: 1.4;
}

.left {
    float: left;
    width: 63%;
    font-size: 9.5px;
}

.right {
    float: right;
    width: 37%;
    text-align: right;
    font-weight: 600;
    font-size: 9.5px;
}

/* === SEPARATOR === */
.separator {
    border-top: 1px dashed #ccc;
    margin: 6px 0;
}

/* === TOTAL === */
.total {
    border-top: 2.5px double #000;
    margin-top: 8px;
    padding-top: 6px;
    font-weight: bold;
    font-size: 10.5px;
    background: #fffef7;
    padding: 6px 4px 4px 4px;
    margin-left: -4px;
    margin-right: -4px;
}

.total .left,
.total .right {
    font-size: 10.5px;
}

/* === WARNA === */
.red {
    color: #d32f2f;
}

/* === FOOTER === */
.footer {
    margin-top: 15px;
    padding-top: 8px;
    border-top: 1px solid #ddd;
    text-align: center;
    font-size: 9px;
    color: #555;
}

.signature {
    margin-top: 50px;
    text-align: center;
    font-size: 10px;
}

.signature-line {
    display: inline-block;
    width: 120px;
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
