<?php
// config/pointsys.php
return [
    'api_key' => env('POINTSYS_API_KEY'),
    'base_url' => env('POINTSYS_BASE_URL', 'https://pointsys.clarastars.com/api/v1'),
    'timeout' => env('POINTSYS_TIMEOUT', 30),
];
