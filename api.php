<?php

namespace app;

use ngatngay\fs;
use ngatngay\http\request;

define('ACCESS', 1);

require '_init.php';

$act = (string) request::get('act');

switch ($act) {
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

    case 'search':
        $q = (string) request::get('q');
        $t = (string) request::get('t', 'all');

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
