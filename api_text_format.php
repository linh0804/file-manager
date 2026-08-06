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

$content = (string) ($_POST['content'] ?? '');
$format_type = strtolower(
    trim((string) ($_POST['format'] ?? file_get_ext($name)))
);

$formatted_content = $content;
$format_error = '';

switch ($format_type) {
    case 'php':
        if ($content === '') {
            break;
        }

        $config_file = __DIR__ . '/php-cs-fixer.config.php';
        $fixer_file = __DIR__ . '/vendor/bin/php-cs-fixer';
        $temp_file = create_tmp_file('fixer');

        $format_error =
            'Không thành công! Yêu cầu chạy "composer install"!';

        if (
            $temp_file !== false
            && is_file($fixer_file)
            && function_can_use('exec')
            && file_put_contents($temp_file, $content) !== false
        ) {
            @chmod($fixer_file, 0755);
            @putenv('PHP_CS_FIXER_IGNORE_ENV=1');

            $output = [];
            $exit_code = -1;

            @exec(
                escapeshellarg($fixer_file)
                . ' fix '
                . escapeshellarg($temp_file)
                . ' --config '
                . escapeshellarg($config_file),
                $output,
                $exit_code
            );

            if ($exit_code === 0) {
                $formatted = file_get_contents($temp_file);

                if ($formatted !== false) {
                    $formatted_content = $formatted;
                    $format_error = '';
                }
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
        $parser_map = [
            'js' => 'babel',
            'html' => 'html',
            'ts' => 'typescript',
            'css' => 'css',
            'scss' => 'scss',
            'json' => 'json',
            'yaml' => 'yaml'
        ];

        $temp_file = create_tmp_file('prettier');

        if (
            $temp_file !== false
            && file_put_contents($temp_file, $content) !== false
        ) {
            $options = [
                '--print-width=1000000',
                '--tab-width=4',
                '--quote-props=preserve',
                '--parser=' . escapeshellarg($parser_map[$format_type])
            ];

            $result = run_command(
                'prettier '
                . implode(' ', $options)
                . ' '
                . escapeshellarg($temp_file)
            );

            $formatted_content = $result['out'] !== ''
                ? $result['out']
                : $content;
            $format_error = $result['err'];

            @unlink($temp_file);
        }

        break;

    default:
        break;
}

response_api([
    'status' => $format_error === '',
    'msg' => $format_error,
    'data' => $formatted_content
]);
