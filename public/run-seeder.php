<?php
// One-time seeder runner — DELETE THIS FILE after use
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || ($_GET['token'] ?? '') !== 'hotelhub_seed_2024') {
    http_response_code(403);
    exit('Forbidden');
}

chdir(dirname(__DIR__));
$output = shell_exec('php artisan db:seed --class=PhatakHotelMenuSeeder 2>&1');
echo '<pre>' . htmlspecialchars($output) . '</pre>';
