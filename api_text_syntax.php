<?php

define('ACCESS', true);
require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$dir = dirname($curr_path);
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

if (file_get_ext($name) !== 'php') {
    $data['message'] = 'Chỉ hỗ trợ kiểm tra cú pháp PHP';
    response($data);
}

if (empty($_POST['content'])) {
    $data['message'] = 'Chưa nhập nội dung';
    response($data);
}

if (!function_can_use('exec')) {
    $data['message'] = 'Hệ thống chặn kiểm tra';
    response($data);
}

$content = (string) $_POST['content'];
$temp_file = create_tmp_file('syntax');

if (
    $temp_file === false
    || file_put_contents($temp_file, $content) === false
) {
    if ($temp_file !== false) {
        @unlink($temp_file);
    }

    $data['message'] = 'Không thể tạo file tạm';
    response($data);
}

$output = [];
$exit_code = -1;

@exec(
    'php -l '
    . escapeshellarg($temp_file),
    $output,
    $exit_code
);

@unlink($temp_file);

if ($exit_code === 0) {
    $data['status'] = true;
    $data['message'] = 'Không có lỗi cú pháp';
} else {
    $data['message'] = 'Có lỗi cú pháp';
    $data['error'] = implode(PHP_EOL, $output);
}

response($data);
