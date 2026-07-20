<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pengaturan Absensi Perusahaan
    |--------------------------------------------------------------------------
    */

    'office_latitude' => env('OFFICE_LATITUDE', -6.8508137608568),
    'office_longitude' => env('OFFICE_LONGITUDE', 107.63763214234),
    'max_distance_radius' => (int) env('MAX_DISTANCE_RADIUS', 30000),

    'clock_in_time' => env('CLOCK_IN_TIME', '08:00'),
    'clock_out_time' => env('CLOCK_OUT_TIME', '17:00'),
    'late_tolerance_minutes' => (int) env('LATE_TOLERANCE_MINUTES', 0),

    'fonnte_token' => env('FONNTE_TOKEN'),
    'whatsapp_group_target' => env('WHATSAPP_GROUP_TARGET'),

    'qr_code_secret' => env('QR_CODE_SECRET', 'MANDIRI-MOTOR-SECRET-CODE'),
];
