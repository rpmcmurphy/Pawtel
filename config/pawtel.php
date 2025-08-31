<?php

return [
    'api' => [
        'base_url' => env('PAWTEL_API_URL', 'http://furbabiessafety.local/api'),
        'timeout' => env('PAWTEL_API_TIMEOUT', 30),
    ],

    'theme' => [
        'colors' => [
            'primary' => '#EC4899',     // Pink
            'secondary' => '#9333EA',   // Purple
            'accent' => '#FBBF24',      // Yellow
            'light' => '#FDF2F8',       // Light pink
            'dark' => '#4C1D95',        // Dark purple
        ],
        'fonts' => [
            'heading' => 'Comic Neue',
            'body' => 'Delius',
        ]
    ],

    'features' => [
        'booking' => [
            'hotel' => true,
            'spa' => true,
            'spay_neuter' => true,
        ],
        'shop' => true,
        'community' => true,
        'admin' => true,
    ],

    'uploads' => [
        'max_size' => env('UPLOAD_MAX_SIZE', 10240), // 10MB in KB
        'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'],
        'paths' => [
            'booking_documents' => 'uploads/bookings',
            'products' => 'uploads/products',
            'posts' => 'uploads/posts',
        ]
    ],

    'pagination' => [
        'per_page' => 15,
        'admin_per_page' => 25,
    ],

    'cache' => [
        'categories_ttl' => 3600, // 1 hour
        'products_ttl' => 1800,   // 30 minutes
        'posts_ttl' => 900,       // 15 minutes
    ]
];
