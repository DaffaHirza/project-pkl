<?php

return [
    'max_snippet_chars' => 3000,
    'fallback_paragraphs' => 3,

    // Definisi bagian-bagian dari LAPORAN UTAMA yang akan divalidasi
    'laporan_sections' => [
        'identitas' => [
            'keywords' => [
                'identitas',
                'nama',
                'jabatan',
                'unit kerja',
                'nip',
                'data pribadi',
            ],
            'check_against' => ['resume', 'sertifikat'], // Cek identitas hanya ke resume & sertifikat
            'instruction' => 'Validasi apakah identitas (nama, jabatan, unit kerja) di laporan utama sesuai dengan dokumen pendukung.',
        ],
        'latar_belakang' => [
            'keywords' => [
                'latar belakang',
                'pendahuluan',
                'background',
                'dasar',
            ],
            'check_against' => ['proposal'], // Cek latar belakang hanya ke proposal
            'instruction' => 'Validasi apakah latar belakang/pendahuluan di laporan utama sesuai dengan proposal.',
        ],
        'tujuan' => [
            'keywords' => [
                'tujuan',
                'objektif',
                'sasaran',
            ],
            'check_against' => ['proposal', 'kertas_kerja'], // Cek tujuan ke proposal & kertas kerja
            'instruction' => 'Validasi apakah tujuan/objektif di laporan utama sesuai dengan proposal dan kertas kerja.',
        ],
        'metodologi' => [
            'keywords' => [
                'metodologi',
                'metode',
                'cara kerja',
                'prosedur',
            ],
            'check_against' => ['kertas_kerja'], // Cek metodologi hanya ke kertas kerja
            'instruction' => 'Validasi apakah metodologi/metode di laporan utama sesuai dengan kertas kerja.',
        ],
        'hasil' => [
            'keywords' => [
                'hasil',
                'temuan',
                'kesimpulan',
                'output',
            ],
            'check_against' => ['kertas_kerja'], // Cek hasil ke kertas kerja
            'instruction' => 'Validasi apakah hasil/temuan di laporan utama sesuai dengan kertas kerja.',
        ],
        'sertifikasi' => [
            'keywords' => [
                'sertifikat',
                'sertifikasi',
                'lisensi',
                'penghargaan',
            ],
            'check_against' => ['sertifikat'], // Cek sertifikasi hanya ke dokumen sertifikat
            'instruction' => 'Validasi apakah sertifikat/lisensi yang disebutkan di laporan utama sesuai dengan dokumen sertifikat.',
        ],
    ],
];
