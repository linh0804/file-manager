<?php

use nightmare\json;

define('ACCESS', true);
define('LOGIN_BYPASS_AUTO_REDIRECT', true);

require __DIR__ . '/_init.php';

// check cron
$tmp_cron = __DIR__ . '/tmp_cron';
$last = (int) @filemtime($tmp_cron);

if ($last >= (time() - 24 * 3600)) {
    exit();
}

@touch($tmp_cron);

// clean login fail
foreach (glob(__DIR__ . '/tmp_login_*') ?: [] as $f) {
    @unlink($f);
}

// updater
$tmp_update_json = __DIR__ . '/tmp_app_update';

if (!file_import($tmp_update_json, REMOTE_VERSION_URL, 15)) {
    exit('<div class="tips">get version info error</div>');
}

$remote = json::decode((string) @file_get_contents($tmp_update_json));

if (empty($remote) || empty($remote['version'])) {
    exit('<div class="tips">can not get update info</div>');
}

if (!version_compare((string) $remote['version'], APP_VERSION, '>')) {
    exit();
}

if (app_reinstall()) {
    exit('<div class="tips">auto update success</div>');
}

exit('<div class="tips">auto update error</div>');
