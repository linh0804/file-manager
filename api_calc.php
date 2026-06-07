<?php

use nightmare\fs;
use nightmare\http\request;

define('ACCESS', true);

require __DIR__ . '/_init.php';

$curr_path = (string) request::post('path');

if (!file_exists($curr_path)) {
    response([
        'status' => false,
        'msg' => 'file not found'
    ])->send();
}

$data = [
    'total_file' => 0,
    'total_dir' => 0,
    'total_size' => 0,
    'total_size_readable' => ''
];

if (is_file($curr_path)) {
    $data['total_file'] = 1;
    $data['total_size'] = filesize($curr_path);
} else {
    $files = read_full_dir($curr_path);
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
