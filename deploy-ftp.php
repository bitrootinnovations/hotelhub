<?php

/**
 * SFTP Deployment Script for HotelHub
 * -------------------------------------
 * Run from project root:
 *   php deploy-ftp.php
 *
 * Reads credentials from ftp-config.php and uploads
 * configured folders/files to the remote server via SFTP (port 22).
 */

require __DIR__ . '/vendor/autoload.php';

use phpseclib3\Net\SFTP;

$config = require __DIR__ . '/ftp-config.php';

// ── Validate config ────────────────────────────────────────────────────────────
if ($config['host'] === '0.0.0.0' || $config['user'] === 'your_ftp_username') {
    die("❌  Please update ftp-config.php with your actual credentials first.\n");
}

echo "🔌  Connecting to {$config['host']}:{$config['port']} via SFTP ...\n";

// ── Connect ────────────────────────────────────────────────────────────────────
$sftp = new SFTP($config['host'], $config['port']);

if (!$sftp->login($config['user'], $config['password'])) {
    die("❌  SFTP login failed. Check host/user/password in ftp-config.php\n");
}

echo "✅  Connected and logged in.\n\n";

// ── Counters ───────────────────────────────────────────────────────────────────
$uploaded = 0;
$failed   = 0;
$skipped  = 0;

// ── Helper: create remote directory recursively ────────────────────────────────
function sftp_mkdir_recursive(SFTP $sftp, string $path): void
{
    if ($sftp->is_dir($path)) return;
    $parts   = explode('/', ltrim($path, '/'));
    $current = '';
    foreach ($parts as $part) {
        if ($part === '') continue;
        $current .= '/' . $part;
        if (!$sftp->is_dir($current)) {
            $sftp->mkdir($current);
        }
    }
}

// ── Helper: check if path is excluded ─────────────────────────────────────────
function is_excluded(string $name, array $exclude): bool
{
    foreach ($exclude as $ex) {
        if ($name === basename($ex) || $name === $ex) return true;
    }
    return false;
}

// ── Helper: upload single file ─────────────────────────────────────────────────
function upload_file(SFTP $sftp, string $localFile, string $remoteFile): bool
{
    global $uploaded, $failed;
    sftp_mkdir_recursive($sftp, dirname($remoteFile));
    if ($sftp->put($remoteFile, $localFile, SFTP::SOURCE_LOCAL_FILE)) {
        echo "  ✔  " . str_replace(DIRECTORY_SEPARATOR, '/', substr($localFile, strlen(__DIR__))) . "\n";
        $uploaded++;
        return true;
    }
    echo "  ✖  FAILED: $localFile\n";
    $failed++;
    return false;
}

// ── Helper: upload directory recursively ──────────────────────────────────────
function upload_directory(SFTP $sftp, string $localDir, string $remoteDir, array $exclude): void
{
    global $skipped;
    $items = scandir($localDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $localPath  = $localDir  . DIRECTORY_SEPARATOR . $item;
        $remotePath = $remoteDir . '/' . $item;

        if (is_excluded($item, $exclude)) {
            echo "  ⤵  Skipped: $item\n";
            $skipped++;
            continue;
        }

        if (is_dir($localPath)) {
            upload_directory($sftp, $localPath, $remotePath, $exclude);
        } else {
            upload_file($sftp, $localPath, $remotePath);
        }
    }
}

// ── Main upload loop ───────────────────────────────────────────────────────────
$local   = rtrim($config['local_path'], DIRECTORY_SEPARATOR);
$remote  = rtrim($config['remote_path'], '/');
$exclude = $config['exclude'];

// Ensure remote base directory exists
sftp_mkdir_recursive($sftp, $remote);

echo "📤  Uploading to: $remote\n";
echo str_repeat('─', 60) . "\n";

foreach ($config['upload'] as $entry) {
    $localPath  = $local  . DIRECTORY_SEPARATOR . $entry;
    $remotePath = $remote . '/' . $entry;

    if (!file_exists($localPath)) {
        echo "  ⚠  Not found locally, skipped: $entry\n";
        continue;
    }

    if (is_dir($localPath)) {
        echo "\n📁  $entry/\n";
        upload_directory($sftp, $localPath, $remotePath, $exclude);
    } else {
        upload_file($sftp, $localPath, $remotePath);
    }
}

// ── Summary ────────────────────────────────────────────────────────────────────
echo str_repeat('─', 60) . "\n";
echo "✅  Done.  Uploaded: $uploaded  |  Failed: $failed  |  Skipped: $skipped\n";

if ($failed > 0) {
    echo "⚠   $failed file(s) failed — check the output above.\n";
}
