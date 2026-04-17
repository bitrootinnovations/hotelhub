<?php
// Temporary migration runner — delete after use
chdir(dirname(__DIR__));
$output = shell_exec('php artisan migrate --force 2>&1');
echo nl2br(htmlspecialchars($output));
