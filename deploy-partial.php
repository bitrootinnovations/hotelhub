<?php

/**
 * Partial deploy — uploads only specific changed/new files.
 * Run: php deploy-partial.php
 */

require __DIR__ . '/vendor/autoload.php';

use phpseclib3\Net\SFTP;

$config = require __DIR__ . '/ftp-config.php';

$sftp = new SFTP($config['host'], $config['port']);
if (!$sftp->login($config['user'], $config['password'])) {
    die("❌  SFTP login failed.\n");
}
echo "✅  Connected to {$config['host']}\n";

$localBase  = __DIR__;
$remoteBase = $config['remote_path'];

// Files to upload (relative to project root)
$files = [
    'resources/views/layouts/header.blade.php',
];

foreach ($files as $rel) {
    $local  = $localBase  . '/' . $rel;
    $remote = $remoteBase . '/' . $rel;

    if (!file_exists($local)) {
        echo "⚠   SKIP (not found): $rel\n";
        continue;
    }

    // Ensure remote directory exists
    $remoteDir = dirname($remote);
    if (!$sftp->is_dir($remoteDir)) {
        $sftp->mkdir($remoteDir, -1, true);
    }

    if ($sftp->put($remote, $local, SFTP::SOURCE_LOCAL_FILE)) {
        echo "  ✔  $rel\n";
    } else {
        echo "  ✘  FAILED: $rel\n";
    }
}

echo "\n✅  Partial deploy done.\n";
echo "\nNow run migrations on server:\n";
echo "  ssh {$config['user']}@{$config['host']} 'cd {$remoteBase} && php artisan migrate --force'\n";
