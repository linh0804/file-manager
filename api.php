<?php

namespace app;

use nightmare\fs;
use nightmare\http\request;

define('ACCESS', 1);

require '_init.php';

$act = (string) request::post('act');

switch ($act) {

case 'calc':
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
    break;

    case 'search':
        $q = (string) request::post('q');
        $t = (string) request::post('t', 'all');

        $q = rtrim($q, '/');

        if (empty($q)) {
            $q = '/';
        }

        // Nếu $q là thư mục thật => scan nó
        if (is_dir($q)) {
            $dir = $q;
        } else {
            // Ngược lại scan thư mục cha
            $dir = dirname($q);
            if ($dir === '/' || $dir === '.') {
                $dir = '/';
            }
        }

        if (!is_dir($dir)) {
            return [];
        }

        $items = scandir($dir);
        $result = [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . '/' . $item;
            $is_dir = is_dir($path);

            if ($t === 'file' && $is_dir) {
                continue;
            }
            if ($t === 'dir' && !$is_dir) {
                continue;
            }

            $real_path = realpath($path);
            if ($is_dir && substr($real_path, -1) !== '/') {
                $real_path .= '/';
            }

            $result[] = $real_path;
        }

        response([
            'status' => true,
            'data' => $result
        ])->send();
        break;

    default:
        response(['status' => false,'msg' => 'action error'])->send();
}
