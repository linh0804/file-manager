<?php


defined('ACCESS') or exit('Not access');

use nightmare\fs;
use nightmare\http\request;

$path = (string) request::post('path');

if (!file_exists($path)) {
    response(['status' => false, 'msg' => 'file not found'])->send();
}

$data = [
    'total_file' => 0,
    'total_dir' => 0,
    'total_size' => 0,
    'total_size_readable' => ''
];

if (is_file($path)) {
    $data['total_file'] = 1;
    $data['total_size'] = filesize($path);
} else {
    $files = read_full_dir($path);
    foreach ($files as $file) {
        if ($file->isFile()) {
            $data['total_file']++;
            $data['total_size'] += $file->getSize();
        }
        if ($file->isDir()) {
            $data['total_dir']++;
        }
    }
}

$data['total_size_readable'] = fs::readable_size($data['total_size']);

response([
    'status' => true,
    'data' => $data
])->send();
