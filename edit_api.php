<?php

namespace app;

use ngatngay\http\request;
use SplFileInfo;

define('ACCESS', true);

require '.init.php';

$path = base64_decode((string) request::get('path'));
$file = new SplFileInfo($path);
$dir = dirname($file->getPathname());
$name = basename($file->getPathname());

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

// thông tin file
$dir = processDirectory($dir);
$path = $dir . '/' . $name;

$content = isset($_POST['content']) ? $_POST['content'] : '';

if (isset($_POST['format'])) {
    $formatType = trim((string) $_POST['format']);

    switch ($formatType) {
        case 'php':
            $configFile = __DIR__ . '/.php-cs-fixer.dist.php';
            $tempFile = __DIR__ . '/tmp/fixer.txt';
            $data = [
                'format' => '',
                'error' => 'Không thành công! Yêu cầu chạy "composer install"!'
            ];

            if (!empty($content)) {
                file_put_contents($tempFile, $content);

                @chmod('vendor/bin/php-cs-fixer', 0775);
                @putenv('PHP_CS_FIXER_IGNORE_ENV=1');
                $result = exec("vendor/bin/php-cs-fixer fix {$tempFile} --config {$configFile}");

                if ($result) {
                    $data['format'] = file_get_contents($tempFile);
                    $data['error'] = '';

                    @unlink($tempFile);
                }
            }
            break;

        case 'js':
        case 'html':
        case 'ts':
        case 'css':
        case 'scss':
        case 'json':
        case 'yaml':
            $opt = [
                '--print-width=1000000',
                '--tab-width=4',
                '--quote-props=preserve'
            ];
            $res = runCommand('prettier ' . implode(' ', $opt) . ' ' . $path);
            $data['format'] = $res['out'] ?: $content;
            $data['error'] = $res['err'];

            break;

        default:
            $data['format'] = $content;
            $data['error'] = '';
    }

    goto end_request;
}


// luu file
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
