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

if (!array_key_exists('content', $_POST)) {
    $data['message'] = 'Chưa nhập nội dung';
    goto end_request;
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

end_request:
@ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

echo json_encode($data);
