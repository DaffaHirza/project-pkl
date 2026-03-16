<?php

return [
    'max_snippet_chars' => 3000,
    'fallback_paragraphs' => 3,

    // Definisi bagian-bagian dari LAPORAN UTAMA yang akan divalidasi
    'laporan_sections' => [
        'identitas_pihak' => [
            'keywords' => [
                'Kepada Yth',
                'Pimpinan',
                'Unit Kerja',
                'Tempat',
                'Alamat',
                'perihal',
            ],
            'check_against' => ['proposal', 'resume'],
            'instruction' => 'Ekstrak nama instansi setelah kata "Pimpinan", lokasi kota. Validasi apakah entitas-entitas tersebut konsisten dengan data yang ada di Resume dan Proposal. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        ],
        'referensi_legalitas' => [
            'keywords' => [
                'Nomor',
                'PKS',
                'Perjanjian Kerja Sama',
                'Surat Perintah',
                'Proposal Penawaran Penilaian No.',
                'Kertas Kerja',
                'aset milik',
            ],
            'check_against' => ['proposal', 'resume', 'kertas_kerja'],
            'instruction' => 'Temukan semua nomor referensi dokumen (PKS, Proposal, Kertas Kerja), tanggal penerbitannya dan aset milik di ambil dari resume penilaian aset. Cocokkan nomor-nomor tersebut dengan dokumen aslinya di file Proposal, Kertas Kerja dan resume. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        ],
        'pemberi_tugas' => [
            'keywords' => [
                'Pemberi Tugas',
                'Alamat',
                'Nama',
                'Jabatan',
                'Kuasa',
            ],
            'check_against' => ['resume', 'kertas_kerja'],
            'instruction' => 'Temukan semua data pemberi tugas (Nama, Jabatan, Alamat, Kuasa) dan cocokkan dengan data di resume dan kertas kerja dan untuk nama dan jabatan diambil setelah kalimat UP: dan kuasa itu diambil dari resume penialian aset. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        ],
        'penggunaan_laporan' => [
            'keywords' => [
                'Pihak yang menggunakan',
                'Alamat',
            ],
            'check_against' => ['resume', 'kertas_kerja'],
            'instruction' => 'Temukan semua data pihak yang menggunakan laporan (Nama, Alamat) dan cocokkan dengan data di resume dan kertas kerja. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        ],
        'objek_penilaian_dan_kepemilikan' => [
            'keywords' => [
                'Objek Penilaian',
                'Kepemilikan',
                'Aset',
                'Alamat',
            ],
            'check_against' => ['resume', 'kertas_kerja'],
            'instruction' => 'Temukan semua data pihak yang menggunakan laporan (Nama, Alamat) dan cocokkan dengan data di resume dan kertas kerja. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        ],
        'tingkat_kedalaman_investigasi' => [
            'keywords' => [
                'Objek Penilaian',
                'Kepemilikan',
                'Aset',
                'Alamat',
            ],
            'check_against' => ['resume', 'kertas_kerja'],
            'instruction' => 'Temukan semua data pihak yang menggunakan laporan (Nama, Alamat) dan cocokkan dengan data di resume dan kertas kerja. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        ],
        'ringkasan_penilaian' => [
            'keywords' => [
                'Laporan Penilaian Aset',
                'Lokasi Aset',
                'Bentuk Kepemilikan',
                'Tanggal Penilaian',
                'Nomor Laporan ',
                'Pemberian Tugas',
                'Pengguna Laporan',
                'Kesimpulan Nilai Pasar',
                'Kesimpulan Nilai Likuidasi',
                'Terbilang',
            ],
            'check_against' => ['resume', 'kertas_kerja'],
            'instruction' => 'Temukan semua data pihak yang menggunakan laporan (Nama, Alamat) dan cocokkan dengan data di resume dan kertas kerja. Pastikan setiap karakter (titik, garis miring, tahun) sama persis tanpa ada kesalahan ketik. Laporkan jika ada perbedaan penulisan atau ketidaksesuaian data.',
        ],
    ],
];
