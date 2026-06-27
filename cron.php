<?php

use nightmare\json;

define('ACCESS', true);
require __DIR__ . '/_init.php';

$tmp_update_json = __DIR__ . '/tmp_app_update';
$last = (int) @filemtime($tmp_update_json);

if ($last >= (time() - 24 * 3600)) {
    exit();
}

@touch($tmp_update_json);

// clean login fail
foreach (glob(APP_PATH . '/tmp_login_*') ?: [] as $f) {
    @unlink($f);
}

// updater
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
