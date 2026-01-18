<div class="slip">
    <div class="header">
        AGUNG PERKASA UTAMA CEMERLANG
        <div class="title">SLIP GAJI</div>
    </div>

    <div class="line"></div>

    <div class="row">
        <div class="left">Tanggal :</div>
        <div class="right">{{ \Carbon\Carbon::parse($p->periode_selesai)->translatedFormat('d F Y') }}</div>
    </div>

    <div class="blue">{{ $p->karyawan->nama }}</div>
    <div class="light">{{ strtoupper($p->karyawan->jabatan ?? '-') }}</div>

    <div class="line"></div>

    <div class="row">
        <div class="left">Masuk : {{ $p->hari_kerja }} hari</div>
        <div class="right">Rp {{ number_format($p->premi_full,0) }}</div>
    </div>

    <div class="row">
        <div class="left">Lembur biasa : {{ $p->jam_lembur_biasa }} jam</div>
        <div class="right">Rp {{ number_format($p->lembur_biasa,0) }}</div>
    </div>

    <div class="row">
        <div class="left">Lembur tgl merah : {{ $p->jam_lembur_tgl_merah }} jam</div>
        <div class="right">Rp {{ number_format($p->lembur_tgl_merah,0) }}</div>
    </div>

    <div class="row">
        <div class="left">Premi Minggu I</div>
        <div class="right">Rp {{ number_format($p->bonus_minggu_1,0) }}</div>
    </div>

    <div class="row">
        <div class="left">Premi Minggu II</div>
        <div class="right">Rp {{ number_format($p->bonus_minggu_2,0) }}</div>
    </div>

    <div class="row">
        <div class="left">Uang Makan</div>
        <div class="right">Rp {{ number_format($p->uang_makan,0) }}</div>
    </div>

    <div class="row">
        <div class="left">Masuk Siang</div>
        <div class="right red">Rp {{ number_format($p->potongan_masuk_siang,0) }}</div>
    </div>

    <div class="row">
        <div class="left">Pot. Kasbon</div>
        <div class="right red">Rp {{ number_format($p->potongan_kasbon,0) }}</div>
    </div>

    <div class="total row">
        <div class="left">JUMLAH</div>
        <div class="right">Rp {{ number_format($p->total_gaji,0) }}</div>
    </div>

    <br>
    <div style="text-align:center">Penerima,</div>
</div>
