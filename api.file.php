<?php

namespace NgatNgay;

define('ACCESS', 1);

require __DIR__ . '/.init.php';

$action = request()->post('action');
$path = request()->post('path');

if (!request()->isMethod('post')) {
    response(['status' => false,'msg' => 'method error'])->send();
}

switch ($action) {
    case 'delete':
        $isDelete = FS::remove($path);
        response([
            'status' => $is_delete,
            'msg' => !$is_delete ? 'Xóa thất bại!' : '',
            'redirect' => $is_delete ? 'index.php?dir=' . dirname($path) : ''
        ])->send();
        break;

    default:
        response(['status' => false,'msg' => 'action error'])->send();
}
