<?php
namespace app;

use ngatngay\http\request;
use ngatngay\fs;

define('ACCESS', 1);

require __DIR__ . '/.init.php';

$action = request::post('action');
$path = (string) request::post('path');
$path = rawurldecode($path);

if (!request::is_method('post')) {
    response(['status' => false,'msg' => 'method error'])->send();
}

switch ($action) {
    case 'calc':
        if (!file_exists($path)) {
            response(['status' => false,'msg' => 'file not found'])->send();
        }
        
        response(['status' => true,'msg' => fs::readable_size(fs::size($path))])->send();
        break;

    case 'delete':
        $isDelete = fs::remove($path);

        response([
            'status' => $isDelete,
            'msg' => !$isDelete ? 'Xóa thất bại!' : '',
            'redirect' => $isDelete ? 'index.php?path=' . dirname($path) : ''
        ])->send();
        break;

    default:
        response(['status' => false,'msg' => 'action error'])->send();
}
