<?php

use Sabre\DAV;

const ACCESS = true;
const LOGIN  = true;

require '.init.php';

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

$server = new DAV\Server(new DAV\FS\Directory('/'));
$server->setBaseUri(request()->server['script_name']);
$server->addPlugin(new DAV\Auth\Plugin($authBackend));
$server->start();
