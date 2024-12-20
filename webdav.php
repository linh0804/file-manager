<?php

use Sabre\DAV;

const ACCESS = true;
const LOGIN  = true;

require '.init.php';

if (!class_exists('Sabre\DAV\Server')) {
    exit('run composer install');
}

$davDir = __DIR__ . '/tmp/webdav';
@mkdir($davDir);

$rootDirectory = new DAV\FS\Directory('/');
$server = new DAV\Server($rootDirectory);
//$server->setBaseUri('/' . basename(__DIR__) . '/webdav.php');

$authBackend = new DAV\Auth\Backend\BasicCallBack(function ($username, $password) use ($configs) {
    if (!ableLogin()) {
        return false;
    }

    if (
        strtolower($username) === strtolower($configs['username'])
        && getPasswordEncode($password) === $configs['password']
    ) {
        removeLoginFail();
        return true;
    } else {
        increaseLoginFail();
    }
        
    return false;
});

$lockBackend = new DAV\Locks\Backend\File($davDir . '/locks');
$lockPlugin = new DAV\Locks\Plugin($lockBackend);

$server->addPlugin(new DAV\Auth\Plugin($authBackend));
$server->addPlugin($lockPlugin);
$server->addPlugin(new DAV\Sync\Plugin());

$server->exec();
