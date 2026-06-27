<?php

use Sabre\DAV\Auth\Backend\BasicCallBack;
use Sabre\DAV\Auth\Plugin;
use Sabre\DAV\FS\Directory;
use Sabre\DAV\Server;

define('ACCESS', true);
define('LOGIN_BYPASS_AUTO_REDIRECT', true);

require __DIR__ . '/_init.php';

$path_info = (string) ($_SERVER["PATH_INFO"] ?? '');
$curr_path = @is_dir($path_info) ? $path_info : dirname($path_info);
$curr_path = $curr_path == '/' &&  $path_info ? '' : $curr_path;
$base_uri = ($_SERVER["SCRIPT_NAME"] ?? '') . rtrim((string) $curr_path, '/');

$auth_backend = new BasicCallBack(function ($username, $password) {
    if (
        strtolower((string) $username) === strtolower((string) config()->get('username'))
        && auth_encode_pwd($password) === config()->get('password')
    ) {
        auth_reset_fail_login();
        return true;
    } else {
        auth_increase_login_fail();
    }

    return false;
});

//error_reporting(0);
try {
    $server = new Server(new Directory($curr_path));
    $server->setBaseUri($base_uri);
    $server->addPlugin(new Plugin($auth_backend));
    $server->start();
} catch (Throwable $e) {
}
