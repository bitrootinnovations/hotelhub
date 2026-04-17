<?php
require __DIR__ . '/vendor/autoload.php';

use phpseclib3\Net\SSH2;

$config = require __DIR__ . '/ftp-config.php';

$ssh = new SSH2($config['host'], $config['port']);
$ssh->login($config['user'], $config['password']);

$remote = $config['remote_path'];
echo $ssh->exec("cd {$remote} && php artisan tinker --execute=\"echo json_encode(DB::table('client_masters')->get(['client_id','client_name','latitude','longitude']));\" 2>&1");
