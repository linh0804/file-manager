<?php
namespace app;

use ngatngay\http\request;
use ngatngay\fs;

define('ACCESS', 1);

require __DIR__ . '/_init.php';

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
        
        /*
            $dir = process_directory($path);
    $dirInfo = new SplFileInfo($dir);
    $files = read_full_dir($dir);

    $dir_size = 0;
    $total_file = 0;
    $total_dir = 0;

    foreach ($files as $file) {
        if ($file->isFile()) {
            $total_file += 1;
            $dir_size += $file->getSize();
        }
        
        if ($file->isDir()) {
            $total_dir += 1;
        }
    }
        */
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
