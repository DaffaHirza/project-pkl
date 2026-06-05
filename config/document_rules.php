<?php

return [    // Line cleaning configuration (to remove repetitive headers/footers)
    'ignore_lines_if_contains' => [
        'KANTOR JASA PENILAI PUBLIK',
        'KANTOR PUSAT',
        'KANTOR CABANG',
        'PURI REGENCY BUSINESS CENTER',
        'Jalan Puri Jambangan Baru',
        'MENARA SUARA MERDEKA',
        'Jl. Pandanaran',
        'Mushofah',
        'kjpp.mdr',
        'kjppmmi.com',
        'kjpp.mmisby',
        'Wilayah Kerja',
        'Halaman',
        'Phone :',
        'Fax :',
        'Email :',
        'Website :',
        'MUSHOFAH DAN REKAN',
        '(d/h. MUSHOFAH MONO IGFIRLY DAN REKAN)',
        'Kep. Menkeu No. 20/MK/SK/2025, No. Izin : 2.15.0132',
        'Penilaian Properti dan konsultan',
    ],

    'ignore_lines_if_regex' => [
        '/^\s*Halaman\s+\d+(\s*\/\s*\d+)?\s*$/iu',
        '/KANTOR\s+(PUSAT|CABANG)/iu',
        '/Phone\s*:\s*/iu',
        '/Email\s*:\s*/iu',
        '/Website\s*:\s*/iu',
        '/Fax\s*:\s*/iu',
        '/kjpp\.?/iu',
    ],

    // Configuration of sections to be audited by AI
    'laporan_sections' => [
        'i// dentitas_pihak' => [
          //   'title' => 'Identitas Pihak',
          //     'keywords' => [
        //         'Kepada yang terhormat',
        //         'Kepada Yth',
        //         'Pimpinan',
        //         'Unit Kerja',
        //         'perihal',
        //     ],
        //     'check_against' => ['proposal', 'resume'],
        //     'mode' => 'compare_documents',
        //     'instruction' => 'Temukan nama instansi/lembaga penerima (seperti nama bank/perusahaan dan divisinya), alamat lengkap instansi, serta nama personil penerima / penanggung jawab yang tercantum setelah kata "UP:" atau "Up." di Laporan Utama. Validasi apakah komponen-komponen ini KONSISTEN SECARA UTUH dengan data di berkas Proposal dan Resume. Jika ada ketidaksesuaian penulisan alamat, nama instansi, atau perbedaan nama personil penerima, laporkan sebagai TIDAK VALID beserta detail perbedaannya.',
        // ],
        // 'referensi_legalitas' => [
        //     'title' => 'Referensi Legalitas',
        //     'keywords' => [
        //         'Perjanjian Kerja Sama',
        //         'Surat Perintah',
        //         'Proposal Penawaran Penilaian No.',
        //         'Kertas Kerja',
        //         'nama pemilik aset',
        //     ],
        //     'check_against' => ['proposal', 'resume', 'kertas_kerja'],
        //     'mode' => 'compare_documents',
        //     'instruction' => 'Temukan semua nomor referensi dokumen (seperti nomor PKS/Perjanjian Kerja Sama, nomor Proposal Penawaran, nomor Kertas Kerja), tanggal penerbitannya, dan nama pemilik aset yang diambil dari dokumen-dokumen tersebut. Cocokkan secara karakter demi karakter (titik, garis miring, spasi, tahun) antara Laporan Utama dengan dokumen aslinya di file Proposal, Kertas Kerja, dan Resume. Kesalahan sekecil apa pun wajib dilaporkan sebagai TIDAK VALID.',
        // ],
        'pemberi_tugas' => [
            'title' => 'Pemberi Tugas',
            'keywords' => [
                'Selaku Pemberi Tugas',
                'Alamat dari pemberi tugas',
                'Nama',
                'Jabatan',
                'Kuasa dari',
            ],
            'check_against' => ['proposal', 'resume'],
            'mode' => 'compare_documents',
            'instruction' => 'Temukan data pemberi tugas (Nama instansi/lembaga, Jabatan/Divisi, Alamat, dan Surat Kuasa) di Laporan Utama, lalu cocokkan dengan data di Proposal dan Resume (ambil nama personil pemberi tugas setelah kata "UP:" atau "Up."). Pastikan setiap data di antara dokumen-dokumen tersebut sama persis secara karakter. Laporkan setiap ketidaksesuaian secara rinci.',
        ],
        'pengguna_laporan' => [
            'title' => 'Pengguna Laporan',
            'keywords' => [
                'Pihak yang menggunakan',
            ],
            'check_against' => ['resume', 'kertas_kerja'],
            'mode' => 'compare_documents',
            'instruction' => 'Temukan pihak-pihak yang didefinisikan sebagai pengguna laporan (Nama instansi/lembaga dan alamatnya) di Laporan Utama, lalu cocokkan dengan data di Resume and Kertas Kerja. Pastikan konsisten tanpa ada perbedaan.',
        ],
        // // 'objek_penilaian_dan_kepemilikan' => [
        // //     'title' => 'Objek Penilaian dan Kepemilikan',
        // //     'keywords' => [
        // //         'Nama dari pemilik objek',
        // //         'Kepemilikan',
        // //         'Aset',
        // //         'Alamat',
        // //     ],
        // //     'check_against' => ['proposal', 'resume'],
        // //     'mode' => 'compare_documents',
        // //     'instruction' => 'Temukan deskripsi objek penilaian dan kepemilikannya (Nama pemilik, alamat lengkap objek, jenis hak milik/sertifikat) di Laporan Utama, lalu cocokkan dengan data di Resume dan Proposal. Laporkan jika ada perbedaan nama pemilik atau deskripsi aset.',
        // // ],
        // // 'kesimpulan_penilaian' => [
        // //     'title' => 'Kesimpulan Penilaian',
        // //     'keywords' => [
        // //         'Kesimpulan Nilai Pasar',
        // //         'Kesimpulan Nilai Likuidasi',
        // //         'Terbilang',
        // //     ],
        // //     'check_against' => ['resume', 'kertas_kerja'],
        // //     'mode' => 'compare_documents',
        // //     'instruction' => 'Temukan data Kesimpulan Nilai Pasar, Kesimpulan Nilai Likuidasi, dan kalimat Terbilang di Laporan Utama. Cocokkan angka nominal dan teks terbilang ini dengan data di Resume dan Kertas Kerja. Angka dan terbilang wajib sama persis.',
        // // ],
        // // 'ringkasan_penilaian' => [
        // //     'title' => 'Ringkasan Penilaian',
        // //     'keywords' => [
        // //         'Laporan Penilaian Aset',
        // //         'Lokasi Aset',
        // //         'Bentuk Kepemilikan',
        // //         'Tanggal Penilaian',
        // //         'Nomor Laporan',
        // //         'Pemberian Tugas',
        // //         'Pengguna Laporan',
        // //         'Kesimpulan Nilai Pasar',
        // //         'Kesimpulan Nilai Likuidasi',
        // //         'Terbilang',
        // //     ],
        //     'check_against' => ['resume', 'kertas_kerja', 'proposal'],
        //     'mode' => 'compare_documents',
        //     'instruction' => 'Audit kecocokan ringkasan penilaian secara keseluruhan antara Laporan Utama dengan Resume, Kertas Kerja, dan Proposal. Ini mencakup lokasi aset, bentuk kepemilikan, nama calon debitur/pemilik, dan nilai penilaian pasar/likuidasi. Pastikan data di bagian ringkasan ini konsisten secara utuh.',
        // ],
        // 'pencapaian_lokasi_dan_peruntukan' => [
        //     'title' => 'Pencapaian Lokasi dan Peruntukan',
        //     'keywords' => [
        //         'Pencapaian Lokasi dan Peruntukan',
        //         'Peruntukan',
        //         'koordinat',
        //         'akses menuju lokasi',
        //         'Situasi lingkungan aset',
        // //     ],
        //     'check_against' => [],
        //     'mode' => 'ai_only',
        //     'instruction' => 'Analisis deskripsi lokasi aset di Laporan Utama: 1) Ekstrak alamat lengkap, kelurahan, kecamatan, kabupaten, provinsi, dan koordinat GPS. 2) Validasi konsistensi administrasi (apakah kecamatan tersebut benar-benar berada di kabupaten/kota tersebut di Indonesia). 3) Cek apakah koordinat GPS logis untuk wilayah tersebut. 4) Cek keselarasan peruntukan tanah dengan deskripsi lingkungan (misal: Ruko di kawasan Komersial, Rumah di Pemukiman). Tentukan status VALID jika logis, atau TIDAK VALID jika ada anomali atau data bertentangan.',
        // ],
        // 'analisis_lingkungan' => [
        //     'title' => 'Analisis Lingkungan',
        //     'keywords' => [
        //         'Analisis Lingkungan',
        //         'Fasilitas umum',
        //         'bangunan disekitar',
        //         'jalan utama',
        //         'kepadatan penduduk',
        //     ],
          'c// heck_against' => [],
            'm// ode' => 'ai_only',
      //       'instruction' => 'Analisis lingkungan sekitar aset di Laporan Utama: 1) Ekstrak daftar fasilitas umum, jalan utama, kepadatan penduduk, dan tipe bangunan sekitar. 2) Cek logika geospasial: apakah fasilitas yang disebutkan wajar berada di kota/kabupaten tersebut. 3) Cek kejanggalan: apakah ada bangunan sekitar yang terduplikasi secara tidak wajar di dalam daftar. Tentukan status VALID jika logis, atau TIDAK VALID jika ada anomali.',
      //   ],
    ],// 
]];

//             {
//             \"status\": \"VALID / TIDAK VALID\",
//             \"score\": 0,
//             \"data\": {
//                 \"wilayah\": \"\",
//                 \"koordinat\": \"\",
//                 \"jalan_utama\": \"\",
//                 \"jalan_depan\": \"\",
//                 \"utilitas\": [],
//                 \"kepadatan\": \"\",
//                 \"pertumbuhan\": \"\",
//                 \"tata_guna\": \"\",
//                 \"fasilitas\": [
//                 {
//                     \"nama\": \"\",
//                     \"kategori\": \"\",
//                     \"jarak\": \"\",
//                     \"status\": \"wajar / perlu verifikasi / anomali\"
//                 }
//                 ],
//                 \"bangunan_sekitar\": []
//             },
//             \"errors\": [],
//             \"catatan\": \"\"
//             }"
//         ],


//     ]

// ];

//             {
//             \"status\": \"VALID / TIDAK VALID\",
//             \"score\": 0,
//             \"data\": {
//                 \"wilayah\": \"\",
//                 \"koordinat\": \"\",
//                 \"jalan_utama\": \"\",
//                 \"jalan_depan\": \"\",
//                 \"utilitas\": [],
//                 \"kepadatan\": \"\",
//                 \"pertumbuhan\": \"\",
//                 \"tata_guna\": \"\",
//                 \"fasilitas\": [
//                 {
//                     \"nama\": \"\",
//                     \"kategori\": \"\",
//                     \"jarak\": \"\",
//                     \"status\": \"wajar / perlu verifikasi / anomali\"
//                 }
//                 ],
//                 \"bangunan_sekitar\": []
//             },
//             \"errors\": [],
//             \"catatan\": \"\"
//             }"
//         ],


//     ]

// ];
