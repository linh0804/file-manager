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

if (file_get_ext($name) !== 'php') {
    response_api([
        'msg' => 'Chỉ hỗ trợ kiểm tra cú pháp PHP'
    ]);
}

if (empty($_POST['content'])) {
    response_api([
        'msg' => 'Chưa nhập nội dung'
    ]);
}

if (!function_can_use('exec')) {
    response_api([
        'msg' => 'Hệ thống chặn kiểm tra'
    ]);
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

    response_api([
        'msg' => 'Không thể tạo file tạm'
    ]);
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
    response_api([
        'status' => true,
        'msg' => 'Không có lỗi cú pháp',
        'data' => ''
    ]);
}

response_api([
    'status' => false,
    'msg' => 'Có lỗi cú pháp',
    'data' => implode(PHP_EOL, $output)
]);
