<?php

namespace app;

use nightmare\http\request;

define('ACCESS', 1);
require __DIR__ . '/../_init.php';

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
