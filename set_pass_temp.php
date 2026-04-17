<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$hash = Illuminate\Support\Facades\Hash::make("navale@123");
DB::table("client_masters")->where("client_id", 1)->update(["password" => $hash]);
echo "Password updated OK\n";
echo "Hash: " . $hash . "\n";
