<div class="slip">

    <div class="header">
        <div class="header-top">
            <div class="header-left">
                <img src="file://{{ public_path('logo.png') }}" class="logo">
            </div>

            <div class="header-center">
                <div class="company">AGUNG PERKASA<br>UTAMA CEMERLANG</div>
                <div class="title">Slip Gaji</div>
            </div>

            <div class="header-right">
                <div class="slip-no">{{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>

        <div class="header-logo">
            <img src="file://{{ public_path('logo.png') }}" class="logo">
        </div>
    </div>
    <div class="content">

        {{-- TANGGAL --}}
        <div class="date-row">
            <span class="date-label">Tanggal :</span>
            <span class="date-value">{{ \Carbon\Carbon::parse($p->periode_selesai)->translatedFormat('d F Y') }}</span>
        </div>

        {{-- NAMA DAN JABATAN --}}
        <div class="blue">{{ strtoupper($p->karyawan->nama) }}</div>
        <div class="light">{{ strtoupper($p->karyawan->jabatan ?? 'KARYAWAN') }}</div>

        {{-- DETAIL GAJI --}}
        <div class="row">
            <div class="left">Gaji Pokok</div>
            <div class="right"> </div>
        </div>

        <div class="row">
            <div class="left">Masuk : {{ $p->hari_kerja }} hari</div>
            <div class="right">Rp {{ number_format($p->premi_full, 0, ',', '.') }}</div>
        </div>

        <div class="row">
            <div class="left">Srt. Hari :</div>
            <div class="right"></div>
        </div>

        <div class="separator"></div>

        <div class="row">
            <div class="left">Lembur biasa : {{ number_format($p->jam_lembur_biasa, 2) }} jam</div>
            <div class="right">Rp {{ number_format($p->lembur_biasa, 0, ',', '.') }}</div>
        </div>

        <div class="row">
            <div class="left">Lembur tgl merah : {{ number_format($p->jam_lembur_tgl_merah, 2) }} jam</div>
            <div class="right">Rp {{ number_format($p->lembur_tgl_merah, 0, ',', '.') }}</div>
        </div>

        <div class="separator"></div>

        <div class="row">
            <div class="left">Premi</div>
            <div class="right"></div>
        </div>

        <div class="row">
            <div class="left">&nbsp;&nbsp;• Minggu I = Full</div>
            <div class="right">Rp {{ number_format($p->bonus_minggu_1, 0, ',', '.') }}</div>
        </div>

        <div class="row">
            <div class="left">&nbsp;&nbsp;• Minggu II = Full</div>
            <div class="right">Rp {{ number_format($p->bonus_minggu_2, 0, ',', '.') }}</div>
        </div>

        <div class="separator"></div>

        <div class="row">
            <div class="left">Uang Makan</div>
            <div class="right">Rp {{ number_format($p->uang_makan, 0, ',', '.') }}</div>
        </div>

        <div class="row">
            <div class="left">Uang Pokok</div>
            <div class="right">Rp {{ number_format($p->lain_lain ?? 0, 0, ',', '.') }}</div>
        </div>

        <div class="separator"></div>

        <div class="row">
            <div class="left">Masuk Siang :</div>
            <div class="right red">Rp {{ number_format($p->potongan_masuk_siang, 0, ',', '.') }}</div>
        </div>

        <div class="row">
            <div class="left">Pot. Kasbon</div>
            <div class="right red">Rp {{ number_format($p->potongan_kasbon, 0, ',', '.') }}</div>
        </div>

        {{-- TOTAL --}}
        <div class="total row">
            <div class="left">JUMLAH</div>
            <div class="right">Rp {{ number_format($p->total_gaji, 0, ',', '.') }}</div>
        </div>

        {{-- TANDA TANGAN --}}
        <div class="signature">
            Penerima,
            <div style="height: 25px;"></div>
            <div class="signature-line"></div>
        </div>

    </div>
</div>
