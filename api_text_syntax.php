<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$file = new SplFileInfo($curr_path);
$dir = dirname($file->getPathname());
$name = basename($file->getPathname());

$data = [
    'status' => false,
    'message' => 'error'
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $data['message'] = 'Phương thức không hợp lệ';
    goto end_request;
}

if (
    empty($dir)
    || empty($name)
    || !is_file(process_directory($dir . '/' . $name))
) {
    $data['message'] = 'Đường dẫn không tồn tại';
    goto end_request;
}

if (!file_is_text($name) && !file_is_unknown($name)) {
    $data['message'] = 'Tập tin này không phải dạng văn bản';
    goto end_request;
}

if (file_get_ext($name) !== 'php') {
    $data['message'] = 'Chỉ hỗ trợ kiểm tra cú pháp PHP';
    goto end_request;
}

if (!array_key_exists('content', $_POST)) {
    $data['message'] = 'Chưa nhập nội dung';
    goto end_request;
}

if (!function_can_use('exec')) {
    $data['message'] = 'Không thể kiểm tra cú pháp';
    goto end_request;
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
    goto end_request;
}

$output = [];
$exit_code = -1;

@exec(
    escapeshellarg(PHP_BINARY)
    . ' -l '
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

end_request:
@ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

echo json_encode($data);
