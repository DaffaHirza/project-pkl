<?php

/* return [
    'max_snippet_chars' => 5000,
    'fallback_paragraphs' => 3,

    // Untuk test cepat 1/beberapa rule saja.
    // Bisa diisi manual: ['pencapaian_lokasi_dan_peruntukan']
    // Atau via ENV DOCUMENT_RULE_ONLY=rule1,rule2
    'only_sections' => env('DOCUMENT_RULE_ONLY')
        ? array_values(array_filter(array_map('trim', explode(',', (string) env('DOCUMENT_RULE_ONLY')))))
        : [],

    // Baris yang cocok dengan marker ini akan dihapus sebelum proses validasi section.
    // Isi dengan kata unik footer template kamu (nama kantor, slogan, dsb).
    'ignore_lines_if_contains' => [
        'KANTOR JASA PENILAI PUBLIK',
        'Halaman',
    ],

    // Regex untuk menghapus baris footer/header yang berulang.
    // Gunakan delimiter regex lengkap, contoh: '/^\s*Halaman\s+\d+(\/\d+)?\s*$/iu'
    // 'ignore_lines_if_regex' => [
    //     '/^\s*Halaman\s+\d+(\s*\/\s*\d+)?\s*$/iu',
    //     '/^\s*(Jl\.?|Jalan)\s+.*(Kecamatan|Kabupaten|Provinsi).*$/iu',
    //     '/^\s*(Telp|Telepon|Fax|Email|Website|www\.).*$/iu',
    // ],

    'laporan_sections' => [
        // 'identitas_pihak' => [
        //     'keywords' => [
        //         'Kepada Yth',
        //         'Pimpinan',
        //         'Unit Kerja',
        //         'Tempat',
        //         'Alamat',
        //         'perihal',
        //     ],
        //     'check_against' => ['proposal', 'resume'],
        //     'instruction' => 'Ekstrak nama instansi setelah kata "Pimpinan", lokasi kota. Validasi apakah entitas-entitas tersebut konsisten dengan data yang ada di Resume dan Proposal. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        // ],
        // 'referensi_legalitas' => [
        //     'keywords' => [
        //         'Perjanjian Kerja Sama',
        //         'Surat Perintah',
        //         'Proposal Penawaran Penilaian No.',
        //         'Kertas Kerja',
        //         'nama pemilik aset',
        //     ],
        //     'check_against' => ['proposal', 'resume', 'kertas_kerja'],
        //     'instruction' => 'Temukan semua nomor referensi dokumen (PKS, Proposal, Kertas Kerja), tanggal penerbitannya dan aset milik di ambil dari resume penilaian aset. Cocokkan nomor-nomor tersebut dengan dokumen aslinya di file Proposal, Kertas Kerja dan resume. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        // ],
        // 'pemberi_tugas' => [
        //     'keywords' => [
        //         'Selaku Pemberi Tugas',
        //         'Alamat dari pemberi tugas',
        //         'Nama',
        //         'Jabatan',
        //         'Kuasa dari',
        //     ],
        //     'check_against' => ['resume', 'kertas_kerja'],
        //     'instruction' => 'Temukan semua data pemberi tugas (Nama, Jabatan, Alamat, Kuasa) dan cocokkan dengan data di resume dan kertas kerja dan untuk nama dan jabatan diambil setelah kalimat UP: dan kuasa itu diambil dari resume penialian aset. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        // ],
        // 'pengguna_laporan' => [
        //     'keywords' => [
        //         'Pihak yang menggunakan',
        //         'Alamat',
        //     ],
        //     'check_against' => ['resume', 'kertas_kerja'],
        //     'instruction' => 'Temukan semua data pihak yang menggunakan laporan (Nama, Alamat) dan cocokkan dengan data di resume dan kertas kerja. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        // ],
        // 'objek_penilaian_dan_kepemilikan' => [
        //     'keywords' => [
        //         'Nama dari pemilik objek',
        //         'Kepemilikan',
        //         'Aset',
        //         'Alamat',
        //     ],
        //     'check_against' => ['proposal', 'resume'],
        //     'instruction' => 'Temukan semua data objek penilaian dan kepemilikan (Nama, Alamat, Kepemilikan) dan cocokkan dengan data di resume dan proposal. Ketika cari di proposal sesuaikan dengan objek penilaian dan bentuk kepemilikan sesuai dengan barisnya. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        // ],
        // 'kesimpulan_penilaian' => [
        //     'keywords' => [
        //         'Kesimpulan Nilai Pasar',
        //         'Kesimpulan Nilai Likuidasi',
        //         'Terbilang',
        //     ],
        //     'check_against' => ['resume', 'kertas_kerja'],
        //     'instruction' => 'Temukan semua data kesimpulan penilaian (Nilai Pasar, Nilai Likuidasi, Terbilang) dan cocokkan dengan data di resume dan kertas kerja. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        // ],
        // 'ringkasan_penilaian' => [
        //     'keywords' => [
        //         'Laporan Penilaian Aset',
        //         'Lokasi Aset',
        //         'Bentuk Kepemilikan',
        //         'Tanggal Penilaian',
        //         'Nomor Laporan ',
        //         'Pemberian Tugas',
        //         'Pengguna Laporan',
        //         'Kesimpulan Nilai Pasar',
        //         'Kesimpulan Nilai Likuidasi',
        //         'Terbilang',
        //     ],
        //     'check_against' => ['resume', 'kertas_kerja'],
        //     'instruction' => 'Temukan semua data pihak yang menggunakan laporan (Nama, Alamat) dan cocokkan dengan data di resume dan kertas kerja. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        // ],
        'pencapaian_lokasi_dan_peruntukan' => [
            'mode' => 'ai_only',
            'keywords' => [
                'Pencapaian Lokasi dan Peruntukan',
                'koordinat',
                'akses menuju lokasi',
            ],
            'instruction' => "Anda adalah sistem verifikasi geospasial dokumen penilaian properti.

            KONTEKS: Dokumen yang Anda terima adalah laporan appraisal properti. Bagian '4.1 Pencapaian Lokasi dan Peruntukan' berisi informasi administratif dan koordinat GPS suatu aset.

            TUGAS ANDA:
            Ekstrak dan validasi data berikut dari teks dokumen:

            1. EKSTRAKSI DATA (lakukan ini terlebih dahulu):
            - Nama Desa / Kelurahan
            - Nama Kecamatan
            - Nama Kabupaten / Kota
            - Provinsi
            - Titik Koordinat (format: derajat lintang/bujur)
            - Deskripsi akses jalan (jika ada urutan jarak, jumlahkan totalnya)
            - Peruntukan tanah yang diklaim

            2. VALIDASI:
            - Periksa apakah hierarki administratif (Desa → Kecamatan → Kabupaten → Provinsi) 
                konsisten dan valid secara geografis Indonesia
            - Periksa apakah koordinat yang tercantum masuk akal untuk berada di wilayah 
                administratif yang diklaim, gunakan pengetahuan geografis Indonesia
            - VALIDASI JARAK: Petunjuk arah di dokumen dimulai dari landmark (misal: Pasar X) 
                menuju aset. Maka total jarak segmen = jarak dari landmark tersebut ke aset. 
                Ini KONSISTEN jika jarak landmark yang sama di bagian fasilitas umum bernilai 
                serupa (selisih wajar ±300 meter). Jangan tandai TIDAK VALID hanya karena 
                total segmen mendekati jarak fasilitas — justru itu tanda konsisten.
            - Periksa apakah peruntukan tanah sesuai dengan deskripsi kawasan

            OUTPUT WAJIB dalam format JSON berikut (tidak boleh ada teks di luar JSON):
            {
            \"status\": \"VALID\" atau \"TIDAK VALID\",
            \"data_ekstraksi\": {
                \"alamat_lengkap\": \"...\",
                \"koordinat\": \"...\",
                \"peruntukan\": \"...\"
            },
            \"catatan\": \"Tulis penjelasan singkat hasil validasi. Jika TIDAK VALID, sebutkan 
            poin spesifik yang tidak konsisten (misal: nama kecamatan tidak sesuai, koordinat 
            di luar wilayah, jarak tidak konsisten).\"
            }",
        ],

        'analisis_lingkungan' => [
            'mode' => 'ai_only',
            'keywords' => [
                'Pencapaian Lokasi',
                'Analisis Lingkungan',
                'Fasilitas umum',
                'bangunan.*sekitar',
            ],
            'instruction' => "Anda adalah sistem audit dokumen penilaian properti bagian lingkungan.
    KONTEKS: Dokumen adalah laporan appraisal properti Indonesia. Bagian 4.1 berisi koordinat GPS dan lokasi administratif aset. Bagian 4.2 berisi fasilitas umum, infrastruktur, dan bangunan sekitar.

    LANGKAH WAJIB — jalankan semua, urut:

    LANGKAH 1 - EKSTRAKSI:
    - Nama Desa, Kecamatan, Kabupaten dari 4.1 → simpan sebagai WILAYAH_REFERENSI
    - Koordinat GPS dari 4.1 (pisahkan nilai lintang dan bujur) → simpan sebagai KOORDINAT_REFERENSI
    - Semua nama bangunan di daftar sekitar lokasi (catat berapa kali tiap nama muncul)
    - Semua fasilitas di 4.2 beserta jaraknya
    - Data jalan dan utilitas

    LANGKAH 2 - CEK KONSISTENSI GEOGRAFIS FASILITAS (WAJIB):
    Untuk SETIAP fasilitas dan bangunan yang disebutkan, gunakan pengetahuan geografi Indonesia Anda untuk menjawab:
    'Di kota/kabupaten mana fasilitas ini SEBENARNYA berada?'
    Lalu bandingkan dengan WILAYAH_REFERENSI dari Langkah 1.
    - Jika fasilitas tersebut dikenal publik berada di kota/kabupaten yang BERBEDA dari WILAYAH_REFERENSI → tandai konsisten_dengan_wilayah: false
    - Jika fasilitas bersifat generik (nama umum yang bisa ada di mana saja, misal: 'Pasar Desa', 'SD Negeri') → tandai true
    - Jika ADA SATU SAJA fasilitas yang false → status keseluruhan = TIDAK VALID

    LANGKAH 3 - CEK KOORDINAT VS WILAYAH:
    - Berdasarkan pengetahuan geografis Indonesia Anda, perkirakan apakah KOORDINAT_REFERENSI masuk akal untuk berada di WILAYAH_REFERENSI (Kecamatan dan Kabupaten dari Langkah 1)
    - Pertimbangkan: apakah nilai lintang (S) dan bujur (E) sesuai dengan posisi kabupaten tersebut di peta Indonesia?
    - Jika koordinat mengarah ke wilayah yang jelas berbeda → tandai TIDAK VALID dan sebutkan wilayah aktual yang lebih mungkin

    LANGKAH 4 - CEK DUPLIKASI TIDAK WAJAR:
    - Jika nama fasilitas atau bangunan yang IDENTIK muncul 2 kali atau lebih dalam daftar bangunan sekitar → ini anomali, tandai TIDAK VALID
    - Catat nama yang duplikat dan jumlah kemunculannya

    LANGKAH 5 - CEK JARAK LOGIS:
    - Jumlahkan total jarak segmen akses dari 4.1
    - Bandingkan dengan jarak fasilitas di 4.2
    - Jika total jarak akses dari landmark lebih besar dari jarak fasilitas yang diklaim lebih jauh → tidak konsisten

    ATURAN PENILAIAN AKHIR:
    Status = TIDAK VALID jika SALAH SATU kondisi berikut terpenuhi:
    (a) Ada fasilitas yang tidak konsisten dengan wilayah aset (Langkah 2)
    (b) Koordinat berada di luar wilayah yang diklaim (Langkah 3)
    (c) Ada nama bangunan/fasilitas yang muncul 2x atau lebih (Langkah 4)
    (d) Jarak akses tidak logis dibanding jarak fasilitas (Langkah 5)
    Status = VALID hanya jika SEMUA langkah lolos tanpa anomali.

    OUTPUT: Kembalikan HANYA JSON berikut, tanpa teks apapun di luar JSON:
    {
    \"status\": \"VALID\" atau \"TIDAK VALID\",
    \"data_ekstraksi\": {
        \"koordinat\": \"...\",
        \"wilayah_diklaim\": \"Desa..., Kec..., Kab...\",
        \"fasilitas\": [
        {\"nama\": \"...\", \"jarak_klaim\": \"...\", \"kategori\": \"pendidikan/kesehatan/belanja/transportasi\", \"konsisten_dengan_wilayah\": true/false}
        ],
        \"infrastruktur_jalan\": \"...\",
        \"kepadatan_penduduk\": \"...\",
        \"bangunan_sekitar\": [\"nama_bangunan (muncul Nx)\"]
    },
    \"anomali_ditemukan\": [
        \"Tulis setiap anomali sebagai string terpisah\"
    ],
    \"catatan\": \"Ringkasan singkat hasil validasi keseluruhan.\"
    }",
        ],
    ],
]; */



return [

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT RULES ENTERPRISE VERSION
    | Universal Prompt Engine
    | Cocok untuk berbagai template appraisal / KJPP / wilayah Indonesia
    |--------------------------------------------------------------------------
    */

    'max_snippet_chars' => 7000,
    'fallback_paragraphs' => 4,

    'laporan_sections' => [

        /*
        |--------------------------------------------------------------------------
        | 4.1 PENCAPAIAN LOKASI DAN PERUNTUKAN
        |--------------------------------------------------------------------------
        */

        'pencapaian_lokasi_dan_peruntukan' => [

            'mode' => 'ai_only',

            'keywords' => [
                'Pencapaian Lokasi dan Peruntukan',
                'Peruntukan',
                'koordinat',
                'akses menuju lokasi',
                'Situasi lingkungan aset',
            ],

            'instruction' => "
            ANDA ADALAH MESIN VALIDATOR GEOSPASIAL DOKUMEN PENILAIAN PROPERTI INDONESIA.

            DOKUMEN BERBEDA-BEDA SETIAP SAAT.
            JANGAN BERGANTUNG PADA CONTOH FILE.
            ANALISA BERDASARKAN ISI TEKS SAAT INI.

            =================================================
            TUJUAN
            =================================================

            Memeriksa apakah bagian lokasi aset konsisten,
            masuk akal, dan tidak mengandung anomali.

            =================================================
            LANGKAH WAJIB
            =================================================

            LANGKAH 1 - EKSTRAK DATA

            Ambil jika tersedia:

            - alamat aset
            - desa / kelurahan
            - kecamatan
            - kabupaten / kota
            - provinsi
            - koordinat GPS
            - nama titik awal perjalanan
            - semua segmen jarak
            - total jarak tempuh
            - radius ke pusat kota/kabupaten
            - peruntukan tanah
            - deskripsi kawasan sekitar

            Jika data tidak ada, isi null.

            -------------------------------------------------

            LANGKAH 2 - VALIDASI ADMINISTRATIF

            Periksa konsistensi:

            Kecamatan -> Kabupaten
            Kabupaten -> Provinsi

            Jika jelas salah wilayah:
            ERROR_KRITIS

            Jika tidak yakin:
            beri catatan PERLU VERIFIKASI

            -------------------------------------------------

            LANGKAH 3 - VALIDASI KOORDINAT

            Gunakan pengetahuan geografis Indonesia.

            Tentukan estimasi wilayah nyata dari koordinat.

            Bandingkan dengan wilayah yang diklaim.

            Jika koordinat jelas berada di daerah lain:
            ERROR_KRITIS

            Jika dekat perbatasan:
            beri toleransi.

            -------------------------------------------------

            LANGKAH 4 - VALIDASI RUTE AKSES

            Jumlahkan seluruh segmen.

            Jika narasi arah / jarak sangat tidak logis:
            ERROR_MINOR

            -------------------------------------------------

            LANGKAH 5 - VALIDASI PERUNTUKAN

            Contoh konsisten:

            Rumah Tinggal ↔ Kawasan Pemukiman
            Gudang ↔ Industri
            Sawah ↔ Pertanian
            Ruko ↔ Komersial

            Jika bertentangan:
            ERROR_KRITIS

            -------------------------------------------------

            LANGKAH 6 - SKOR

            Skor awal = 100

            ERROR_KRITIS = -50
            ERROR_MINOR  = -15

            Jika ada ERROR_KRITIS => TIDAK VALID
            Jika skor < 70 => TIDAK VALID
            Selain itu VALID

            =================================================
            ATURAN WAJIB
            =================================================

            - Jangan mengarang
            - Jangan memakai contoh file lama
            - Fokus hanya isi teks saat ini
            - Jika data kurang, tulis PERLU VERIFIKASI
            - Output JSON saja

            =================================================
            OUTPUT JSON
            =================================================

            {
            \"status\": \"VALID / TIDAK VALID\",
            \"score\": 0,
            \"data\": {
                \"alamat\": \"\",
                \"desa\": \"\",
                \"kecamatan\": \"\",
                \"kabupaten\": \"\",
                \"provinsi\": \"\",
                \"koordinat\": \"\",
                \"titik_awal\": \"\",
                \"segmen_meter\": [],
                \"total_meter\": 0,
                \"radius_pusat\": \"\",
                \"peruntukan\": \"\",
                \"kawasan\": \"\"
            },
            \"estimasi_wilayah_koordinat\": \"\",
            \"errors\": [],
            \"catatan\": \"\"
            }"
        ],


        /*
        |--------------------------------------------------------------------------
        | 4.2 ANALISIS LINGKUNGAN
        |--------------------------------------------------------------------------
        */

        'analisis_lingkungan' => [
            'mode' => 'ai_only',
            'keywords' => [
                'Analisis Lingkungan',
                'Fasilitas umum',
                'bangunan disekitar',
                'jalan utama',
                'kepadatan penduduk',
            ],

            'instruction' => "
            ANDA ADALAH SISTEM AUDIT LINGKUNGAN PROPERTI INDONESIA.

            DOKUMEN BISA BERASAL DARI KOTA MANA PUN.
            JANGAN TERIKAT CONTOH FILE LAMA.

            ANALISA BERDASARKAN TEKS YANG DIBERIKAN.

            =================================================
            TUJUAN
            =================================================

            Memastikan data lingkungan sekitar aset masuk akal,
            konsisten, dan bebas manipulasi.

            =================================================
            LANGKAH WAJIB
            =================================================

            LANGKAH 1 - EKSTRAK DATA

            Ambil:

            - wilayah aset
            - koordinat (jika tersedia)
            - jalan utama
            - jalan depan aset
            - utilitas
            - kepadatan penduduk
            - pertumbuhan bangunan
            - tata guna tanah
            - fasilitas umum + jarak
            - bangunan sekitar

            Jika tidak ada isi null.

            -------------------------------------------------

            LANGKAH 2 - VALIDASI INFRASTRUKTUR

            Cek apakah narasi jalan/utilitas masuk akal.

            Contoh:

            jalan desa beton = wajar
            PLN = umum
            jalan tol di desa terpencil = mencurigakan

            Jika janggal:
            ERROR_MINOR

            -------------------------------------------------

            LANGKAH 3 - VALIDASI FASILITAS

            Untuk tiap fasilitas:

            - Tentukan apakah nama itu generik atau entitas spesifik terkenal.

            Jika jelas beda kota/provinsi:
            ERROR_KRITIS

            -------------------------------------------------

            LANGKAH 4 - VALIDASI JARAK

            Cek apakah jarak fasilitas masuk akal.

            Jika mustahil / bertentangan:
            ERROR_MINOR

            -------------------------------------------------

            LANGKAH 5 - DUPLIKASI

            Jika nama identik muncul >=2 kali dalam daftar bangunan:

            ERROR_KRITIS

            -------------------------------------------------

            LANGKAH 6 - DEMOGRAFI

            Jika narasi saling bertentangan:

            padat penduduk tapi bangunan sangat sedikit

            => ERROR_MINOR

            -------------------------------------------------

            LANGKAH 7 - SKOR

            Skor awal = 100

            ERROR_KRITIS = -40
            ERROR_MINOR  = -10

            Jika ada ERROR_KRITIS => TIDAK VALID
            Jika skor < 70 => TIDAK VALID
            Selain itu VALID

            =================================================
            ATURAN WAJIB
            =================================================

            - Jangan pakai hafalan file lama
            - Fokus isi dokumen sekarang
            - Nama generik jangan dipaksa salah
            - Nama terkenal wajib dicek wilayah
            - Jika ragu tulis PERLU VERIFIKASI
            - Output JSON saja

            =================================================
            OUTPUT JSON
            =================================================

            {
            \"status\": \"VALID / TIDAK VALID\",
            \"score\": 0,
            \"data\": {
                \"wilayah\": \"\",
                \"koordinat\": \"\",
                \"jalan_utama\": \"\",
                \"jalan_depan\": \"\",
                \"utilitas\": [],
                \"kepadatan\": \"\",
                \"pertumbuhan\": \"\",
                \"tata_guna\": \"\",
                \"fasilitas\": [
                {
                    \"nama\": \"\",
                    \"kategori\": \"\",
                    \"jarak\": \"\",
                    \"status\": \"wajar / perlu verifikasi / anomali\"
                }
                ],
                \"bangunan_sekitar\": []
            },
            \"errors\": [],
            \"catatan\": \"\"
            }"
        ],


    ]

];
