<?php

return [
    'deadline' => [
        'timezone' => 'Asia/Jakarta',
        'day' => 'friday', // Jumat
        'time' => '12:30', // WIB
    ],

    'scores' => [
        'duha' => [
            'hadir' => 2,
            'tidak_hadir' => 0,
            'munfarid' => 1,
            'haid' => 0,
            'tidak_sekolah' => 0,
            'tidak_sholat' => -2,
        ],
        'dzuhur' => [
            'hadir' => 2,
            'tidak_hadir' => 0,
            'haid' => 0,
            'tidak_sekolah' => 0,
            'tidak_sholat' => -1,
        ],
        'quran' => [
            'bawa' => 2,
            'tidak_bawa' => -1,
            'tidak_sekolah' => 0,
        ],
        'tadarus' => [
            'ikut' => 2, // bobot default; dapat diubah admin lewat settings (tadarus_weight)
            'tidak' => 0,
        ],
    ],

    'options' => [
        'duha' => [
            'hadir' => 'Hadir',
            'tidak_hadir' => 'Tidak Hadir',
            'munfarid' => 'Munfarid',
            'haid' => 'Haid',
            'tidak_sekolah' => 'Tidak Sekolah',
            'tidak_sholat' => 'Tidak Sholat',
        ],
        'dzuhur' => [
            'hadir' => 'Hadir',
            'tidak_hadir' => 'Tidak Hadir',
            'haid' => 'Haid',
            'tidak_sekolah' => 'Tidak Sekolah',
            'tidak_sholat' => 'Tidak Sholat',
        ],
        'quran' => [
            'bawa' => 'Bawa',
            'tidak_bawa' => 'Tidak Bawa',
            'tidak_sekolah' => 'Tidak Sekolah',
        ],
        'tadarus' => [
            'ikut' => 'Ikut Tadarus',
            'tidak' => 'Tidak',
        ],
    ],
];
