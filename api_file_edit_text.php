<?php

use nightmare\http\request;

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

if (!isset($_POST['request_api'])) {
    goto end_request;
}

if (empty($dir) || empty($name) || !is_file(process_directory($dir . '/' . $name))) {
    $data['message'] = 'Đường dẫn không tồn tại';
    goto end_request;
}

if (!file_is_text($name) && !file_is_unknown($name)) {
    $data['message'] = 'Tập tin này không phải dạng văn bản';
    goto end_request;
}

$content = $_POST['content'] ?? '';

if (isset($_POST['format'])) {
    $format_type = trim((string) $_POST['format']);

    switch ($format_type) {
        case 'php':
            $config_file = __DIR__ . '/php-cs-fixer.config.php';
            $temp_file = create_tmp_file('fixer');
            $data = [
                'format' => '',
                'error' => 'Không thành công! Yêu cầu chạy "composer install"!'
            ];

            if ($temp_file !== false && !empty($content)) {
                file_put_contents($temp_file, $content);

                @chmod('vendor/bin/php-cs-fixer', 0775);
                @putenv('PHP_CS_FIXER_IGNORE_ENV=1');
                $result = exec(
                    'vendor/bin/php-cs-fixer fix '
                    . escapeshellarg($temp_file)
                    . ' --config '
                    . escapeshellarg($config_file)
                );

                if ($result) {
                    $data['format'] = file_get_contents($temp_file);
                    $data['error'] = '';
                }
            }

            if ($temp_file !== false) {
                @unlink($temp_file);
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
            $res = run_command('prettier ' . implode(' ', $opt) . ' ' . $curr_path);
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
$current_owner = fileowner($curr_path);

if (file_put_contents($curr_path, $content) !== false) {
    // fix owner
    @chown($curr_path, $current_owner);

    $data['status'] = true;
    $data['message'] = 'Lưu lại thành công';

    $check_php = isset($_POST['check']) ? (bool) $_POST['check'] : false;

    if ($check_php) {
        $error_syntax = 'Lưu thành công! Không thể kiểm tra lỗi';
        $is_execute = function_can_use('exec');

        if ($is_execute) {
            @exec(PHP_BINARY . ' -c -f -l ' . $curr_path, $output, $value);

            if ($value == -1) {
            } elseif ($value == 255 || count($output) == 3) {
                $data['error'] = $output[1];
                $error_syntax = 'Lưu thành công! Có lỗi!';
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
