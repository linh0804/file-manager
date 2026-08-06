<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$name = basename($curr_path);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    response_api([
        'msg' => 'Phương thức không hợp lệ'
    ]);
}

if (!is_file($curr_path)) {
    response_api([
        'msg' => 'Đường dẫn không tồn tại'
    ]);
}

if (!file_is_text($name) && !file_is_unknown($name)) {
    response_api([
        'msg' => 'Tập tin này không phải dạng văn bản'
    ]);
}

if (!array_key_exists('content', $_POST)) {
    response_api([
        'msg' => 'Chưa nhập nội dung'
    ]);
}

$content = (string) $_POST['content'];
$current_owner = @fileowner($curr_path);

if (file_put_contents($curr_path, $content) !== false) {
    if ($current_owner !== false) {
        @chown($curr_path, $current_owner);
    }

    response_api([
        'status' => true,
        'msg' => 'Lưu lại thành công',
        'data' => ''
    ]);
}

response_api([
    'status' => false,
    'msg' => 'Lưu lại thất bại',
    'data' => ''
]);
