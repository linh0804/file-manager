<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$name = basename($curr_path);

$data = [
    'status' => false,
    'message' => 'error'
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $data['message'] = 'Phương thức không hợp lệ';
    response($data);
}

if (!is_file($curr_path)) {
    $data['message'] = 'Đường dẫn không tồn tại';
    response($data);
}

if (!file_is_text($name) && !file_is_unknown($name)) {
    $data['message'] = 'Tập tin này không phải dạng văn bản';
    response($data);
}

if (!array_key_exists('content', $_POST)) {
    $data['message'] = 'Chưa nhập nội dung';
    response($data);
}

$content = (string) $_POST['content'];
$current_owner = @fileowner($curr_path);

if (file_put_contents($curr_path, $content) !== false) {
    if ($current_owner !== false) {
        @chown($curr_path, $current_owner);
    }

    $data['status'] = true;
    $data['message'] = 'Lưu lại thành công';
} else {
    $data['message'] = 'Lưu lại thất bại';
}

response($data);
