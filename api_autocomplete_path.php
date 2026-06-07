<?php

use nightmare\http\request;

define('ACCESS', true);

require __DIR__ . '/_init.php';

$curr_path = (string) request::post('path');
$curr_path = trim($curr_path);

if ($curr_path === '') {
    $curr_path = '/';
}

if (substr($curr_path, -1) === '/') {
    $dir = $curr_path;
} else {
    $dir = dirname($curr_path);

    if ($dir === '.' || $dir === '') {
        $dir = '/';
    }
}

if (!is_dir($dir) || !is_readable($dir)) {
    response([
        'status' => true,
        'data' => []
    ])->send();
    exit;
}

$items = @scandir($dir, SCANDIR_SORT_NONE);

if (!is_array($items)) {
    response([
        'status' => true,
        'data' => []
    ])->send();
    exit;
}

$dirs = [];
$files = [];

foreach ($items as $item) {
    if ($item === '.' || $item === '..') {
        continue;
    }

    $item_path = rtrim($dir, '/') . '/' . $item;

    if (is_dir($item_path)) {
        $dirs[] = rtrim($item, '/') . '/';
        continue;
    }

    if (is_file($item_path)) {
        $files[] = rtrim($item, '/');
    }
}

sort($dirs, SORT_NATURAL | SORT_FLAG_CASE);
sort($files, SORT_NATURAL | SORT_FLAG_CASE);

response([
    'status' => true,
    'data' => array_merge($dirs, $files)
])->send();
