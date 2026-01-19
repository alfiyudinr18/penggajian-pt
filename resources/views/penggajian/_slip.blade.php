<div class="slip">

    {{-- WATERMARK - Akan selalu muncul --}}
    <div class="watermark-svg">
        <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
            <!-- Circle Background -->
            <circle cx="100" cy="100" r="90" fill="none" stroke="#c9a24d" stroke-width="4" opacity="0.5"/>
            <circle cx="100" cy="100" r="85" fill="none" stroke="#b8860b" stroke-width="2" opacity="0.3"/>

            <!-- Eagle Wings - Lebih detail -->
            <path d="M 100 55 Q 50 70 30 65 Q 35 75 45 80 Q 60 85 80 80 Q 90 75 100 70 Z" fill="#c9a24d" opacity="0.6"/>
            <path d="M 100 55 Q 150 70 170 65 Q 165 75 155 80 Q 140 85 120 80 Q 110 75 100 70 Z" fill="#c9a24d" opacity="0.6"/>

            <!-- Eagle Body & Head -->
            <ellipse cx="100" cy="85" rx="15" ry="25" fill="#b8860b" opacity="0.7"/>
            <circle cx="100" cy="65" r="12" fill="#b8860b" opacity="0.7"/>

            <!-- Beak -->
            <path d="M 110 65 L 118 65 L 110 68 Z" fill="#d4af37" opacity="0.7"/>

            <!-- Eye -->
            <circle cx="105" cy="63" r="2" fill="#333" opacity="0.8"/>

            <!-- Crown -->
            <path d="M 100 50 L 103 58 L 111 58 L 105 63 L 107 71 L 100 66 L 93 71 L 95 63 L 89 58 L 97 58 Z" fill="#ffd700" opacity="0.7"/>

            <!-- Shield - Lebih besar dan jelas -->
            <path d="M 100 100 L 75 105 L 75 140 Q 75 158 100 170 Q 125 158 125 140 L 125 105 Z" fill="#fff" opacity="0.3" stroke="#4a90d9" stroke-width="3"/>

            <!-- Shield Divisions -->
            <line x1="75" y1="122" x2="125" y2="122" stroke="#4a90d9" stroke-width="2" opacity="0.4"/>
            <line x1="100" y1="105" x2="100" y2="170" stroke="#4a90d9" stroke-width="2" opacity="0.4"/>

            <!-- Shield Quarters -->
            <rect x="77" y="107" width="21" height="13" fill="#4a90d9" opacity="0.3"/>
            <rect x="102" y="107" width="21" height="13" fill="#e3f2fd" opacity="0.4"/>
            <rect x="77" y="124" width="21" height="13" fill="#e3f2fd" opacity="0.4"/>
            <rect x="102" y="124" width="21" height="13" fill="#4a90d9" opacity="0.3"/>

            <!-- Center Emblem - APUC -->
            <circle cx="100" cy="135" r="18" fill="#b8860b" opacity="0.5"/>
            <text x="100" y="142" font-family="Arial, sans-serif" font-size="14" font-weight="bold" fill="#2c5f8d" text-anchor="middle" opacity="0.7">APUC</text>

            <!-- Decorative Stars -->
            <circle cx="60" cy="110" r="3" fill="#ffd700" opacity="0.5"/>
            <circle cx="140" cy="110" r="3" fill="#ffd700" opacity="0.5"/>
            <circle cx="60" cy="140" r="3" fill="#ffd700" opacity="0.5"/>
            <circle cx="140" cy="140" r="3" fill="#ffd700" opacity="0.5"/>

            <!-- Bottom Ribbon -->
            <path d="M 65 165 Q 100 172 135 165" fill="none" stroke="#b8860b" stroke-width="3" opacity="0.6"/>
            <path d="M 65 165 L 60 175 L 65 170 Z" fill="#b8860b" opacity="0.5"/>
            <path d="M 135 165 L 140 175 L 135 170 Z" fill="#b8860b" opacity="0.5"/>

            <!-- Top Arc Text Path -->
            <path id="topCurve" d="M 25 100 Q 100 35 175 100" fill="none"/>
            <text font-family="Arial, sans-serif" font-size="11" font-weight="bold" fill="#b8860b" opacity="0.6">
                <textPath href="#topCurve" startOffset="50%" text-anchor="middle">
                    AGUNG PERKASA UTAMA
                </textPath>
            </text>

            <!-- Bottom Arc Text Path -->
            <path id="bottomCurve" d="M 25 100 Q 100 165 175 100" fill="none"/>
            <text font-family="Arial, sans-serif" font-size="11" font-weight="bold" fill="#b8860b" opacity="0.6">
                <textPath href="#bottomCurve" startOffset="50%" text-anchor="middle">
                    CEMERLANG
                </textPath>
            </text>
        </svg>
    </div>

    <div class="content">

        {{-- NOMOR SLIP --}}
        <div class="slip-no">
            NO. {{ str_pad($p->id, 6, '0', STR_PAD_LEFT) }}
        </div>

        {{-- HEADER --}}
        <div class="header">
            <div class="company">AGUNG PERKASA UTAMA CEMERLANG</div>
            <div class="title">SLIP GAJI</div>
        </div>

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
            <div class="right">Rp {{ number_format($p->premi_full, 0, ',', '.') }}</div>
        </div>

        <div class="row">
            <div class="left">Srt. Hari : {{ $p->hari_kerja }} hari</div>
            <div class="right">-</div>
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
            <div class="left">Masuk Siang : {{ $p->telat_count ?? 0 }} hari</div>
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
            <div style="height: 45px;"></div>
            <div class="signature-line"></div>
        </div>

    </div>
</div>
