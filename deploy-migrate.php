<?php

/**
 * Upload run-migrate.php to server, trigger it via HTTP, then delete it.
 * Run: php deploy-migrate.php
 */

require __DIR__ . '/vendor/autoload.php';

use phpseclib3\Net\SFTP;

$config = require __DIR__ . '/ftp-config.php';

$sftp = new SFTP($config['host'], $config['port']);
if (!$sftp->login($config['user'], $config['password'])) {
    die("❌  SFTP login failed.\n");
}
echo "✅  Connected\n";

$remoteBase = $config['remote_path'];

// Upload the migration runner to public/ so it's web-accessible
$localScript  = __DIR__ . '/run-migrate.php';
$remoteScript = $remoteBase . '/public/run-migrate.php';

if ($sftp->put($remoteScript, $localScript, SFTP::SOURCE_LOCAL_FILE)) {
    echo "✔  Uploaded run-migrate.php to /public/\n";
} else {
    die("✘  Upload failed\n");
}

// Trigger via HTTP
echo "🌐  Triggering migration...\n";
$url = 'https://bitrootinnovations.com/hotelhub/public/run-migrate.php';
$result = file_get_contents($url);
echo $result ? strip_tags($result) : "⚠  No response (check URL manually)\n";

// Delete the script after running
$sftp->delete($remoteScript);
echo "\n🗑  Cleaned up run-migrate.php from server\n";
