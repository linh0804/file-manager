<?php


use nightmare\http\request;

define('ACCESS', 1);

require __DIR__ . '/_init.php';

$act = (string) request::post('act');
$act = trim($act);

if ($act === '' || !preg_match('/^[a-z0-9_]+$/', $act)) {
    response(['status' => false, 'msg' => 'action error'])->send();
}

$action_file = __DIR__ . '/api/' . $act . '.php';

if (!file_exists($action_file)) {
    response(['status' => false, 'msg' => 'action error'])->send();
}

require $action_file;
