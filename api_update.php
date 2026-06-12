<?php

use nightmare\json;

define('ACCESS', true);

require __DIR__ . '/_init.php';

$last = (int) config()->get('app_update_last');
$tmp_update_json = __DIR__ . '/tmp_app_update';

if ($last >= (time() - 24 * 3600)) {
    exit();
}

config()->set('app_update_last', time());

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
