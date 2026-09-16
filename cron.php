<?php

use Nightmare\Json;

define('ACCESS', true);
define('LOGIN_BYPASS_AUTO_REDIRECT', true);

require __DIR__ . '/_init.php';

// check cron
$tmp_cron = __DIR__ . '/tmp_cron';
$last = (int) @filemtime($tmp_cron);

if ($last >= (time() - 24 * 3600)) {
    exit;
}

@touch($tmp_cron);

// clean login fail
foreach (glob(__DIR__ . '/tmp_login_*') ?: [] as $f) {
    @unlink($f);
}

// updater
$tmp_update_json = __DIR__ . '/tmp_app_update';

if (!file_import($tmp_update_json, REMOTE_VERSION_URL, 15)) {
    exit('get version info error');
}

$remote = Json::decode((string) @file_get_contents($tmp_update_json));

if (empty($remote) || empty($remote['version'])) {
    exit('can not get update info');
}

if (!version_compare((string) $remote['version'], APP_VERSION, '>')) {
    exit;
}

if (app_reinstall()) {
    exit('auto update success');
}

exit('auto update error');
