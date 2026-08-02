<?php

use Nightmare\Json;

define('ACCESS', true);
define('LOGIN_BYPASS_AUTO_REDIRECT', true);

require __DIR__ . '/_init.php';

// check cron
$tmp_cron = __DIR__ . '/tmp_cron';
$last = (int) @filemtime($tmp_cron);

if ($last >= (time() - 24 * 3600)) {
    response(['data' => '']);
}

@touch($tmp_cron);

// clean login fail
foreach (glob(__DIR__ . '/tmp_login_*') ?: [] as $f) {
    @unlink($f);
}

// updater
$tmp_update_json = __DIR__ . '/tmp_app_update';

if (!file_import($tmp_update_json, REMOTE_VERSION_URL, 15)) {
    response(['data' => '<div class="tips">get version info error</div>']);
}

$remote = Json::decode((string) @file_get_contents($tmp_update_json));

if (empty($remote) || empty($remote['version'])) {
    response(['data' => '<div class="tips">can not get update info</div>']);
}

if (!version_compare((string) $remote['version'], APP_VERSION, '>')) {
    response(['data' => '']);
}

if (app_reinstall()) {
    response(['data' => '<div class="tips">auto update success</div>']);
}

response(['data' => '<div class="tips">auto update error</div>']);
