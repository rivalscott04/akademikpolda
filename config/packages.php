<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Package Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk sistem paket berlangganan
    |
    */

    'package_limits' => [
        'free' => [
            'max_tryouts' => 1,
            'description' => '1 tryout dari semua jenis, unlimited attempts'
        ],
        'bahasa_inggris' => [
            'max_tryouts' => 10,
            'description' => 'Tryout Bahasa Inggris'
        ],
        'pu' => [
            'max_tryouts' => 10,
            'description' => 'Tryout Pengetahuan Umum'
        ],
        'twk' => [
            'max_tryouts' => 10,
            'description' => 'Tryout Tes Wawasan Kebangsaan'
        ],
        'numerik' => [
            'max_tryouts' => 10,
            'description' => 'Tryout Numerik'
        ],
        'lengkap' => [
            'max_tryouts' => 15,
            'description' => 'Paket lengkap mencakup semua tes akademik'
        ]
    ],

    // Mapping untuk backward compatibility dengan menu pricing lama
    'legacy_mapping' => [
        'psikologi' => 'akademik', // Menu pricing lama 'psikologi' = 'akademik' baru
    ],

    // Package mapping sekarang fully dynamic dari database
    // Tidak perlu hardcode mapping di config
];
