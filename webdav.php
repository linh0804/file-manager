<?php
namespace app;

use Sabre\DAV\Auth\Backend\BasicCallBack;
use Sabre\DAV\Server;
use Sabre\DAV\FS\Directory;
use Sabre\DAV\Auth\Plugin;
use nightmare\http\request;
use Throwable;

define('ACCESS', true);
define('LOGIN', true);

require '_init.php';

$path_info = (string) request::server('path_info');
$path = @is_dir($path_info) ? $path_info : dirname($path_info);
$path = $path == '/' &&  $path_info ? '' : $path;
$base_uri = request::server('script_name') . rtrim((string) $path, '/');

$authBackend = new BasicCallBack(function ($username, $password) use ($configs) {
    if (!able_login()) {
        return false;
    }

    if (
        strtolower((string) $username) === strtolower((string) $configs['username'])
        && get_password_encode($password) === $configs['password']
    ) {
        remove_login_fail();
        return true;
    } else {
        increase_login_fail();
    }
        
    return false;
});

//error_reporting(0);
try {
$server = new Server(new Directory($path));
$server->setBaseUri($base_uri);
$server->addPlugin(new Plugin($authBackend));
//$server->addPlugin(new DAV\Browser\Plugin());
$server->start();
} catch(Throwable $e) {}
