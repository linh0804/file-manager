<?php

define('ACCESS', true);

require '.init.php';

$data = [
    'status' => false,
    'message' => 'error'
];

if (!isset($_POST['requestApi'])) {
    goto end_request;
}

if ($dir == null || $name == null || !is_file(processDirectory($dir . '/' . $name))) {
    $data['message'] = 'Đường dẫn không tồn tại';
    goto end_request;
}


if (!isFormatText($name) && !isFormatUnknown($name)) {
    $data['message'] = 'Tập tin này không phải dạng văn bản';
    goto end_request;
}

if (isset($_POST['format_php'])) {
    $configFile = __DIR__ . '/.php-cs-fixer.dist.php';
    $tempFile = __DIR__ . '/tmp/fixer.txt';
    //$cacheFile = __DIR__ . '/tmp/.php-cs-fixer.cache';

    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $data = array(
        'format' => '',
        'error' => 'Không thành công! Yêu cầu chạy "composer install"!'
    );

    if (!empty($content)) {
        file_put_contents($tempFile, $content);
        
        @chmod('vendor/bin/php-cs-fixer', 0775);
        $result = exec("vendor/bin/php-cs-fixer fix {$tempFile} --config {$configFile}");

        if ($result) {
            $data['format'] = file_get_contents($tempFile);
            $data['error'] = '';

            @unlink($tempFile);
            //@unlink($cacheFile);
        }
    }

    goto end_request;
}


// luu file
$dir = processDirectory($dir);
$path = $dir . '/' . $name;

if (!isset($_POST['content'])) {
    $data['message'] = 'Chưa nhập nội dung';
    goto end_request;
}

$content = $_POST['content'];
$currentOwner = fileowner($path);

if (file_put_contents($path, $content) !== false) {
    // fix owner
    @chown($path, $currentOwner);

    $data['status'] = true;
    $data['message'] = 'Lưu lại thành công';

    $checkPHP = isset($_POST['check']) ? (bool) $_POST['check'] : false;

    if ($checkPHP) {
        $error_syntax = 'Lưu thành công! Không thể kiểm tra lỗi';
        $isExecute = isFunctionExecEnable();

        if ($isExecute) {
            @exec(getPathPHP() . ' -c -f -l ' . $path, $output, $value);

            if ($value == -1) {
            } elseif ($value == 255 || count($output) == 3) {
                $error_syntax = 'Lưu thành công! Có lỗi!';

                $data['error'] = $output[1];
            } else {
                $error_syntax = 'Lưu thành công! Không có lỗi';
            }
        }

        $data['message'] = $error_syntax;
    }
} else {
    $data['message'] = 'Lưu lại thất bại';
}

// response
end_request:
@ob_end_clean();
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
