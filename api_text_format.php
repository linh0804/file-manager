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

$content = (string) ($_POST['content'] ?? '');
$format_type = strtolower(
    trim((string) ($_POST['format'] ?? file_get_ext($name)))
);

$data = [
    'format' => $content,
    'error' => ''
];

switch ($format_type) {
    case 'php':
        if ($content === '') {
            break;
        }

        $config_file = __DIR__ . '/php-cs-fixer.config.php';
        $fixer_file = __DIR__ . '/vendor/bin/php-cs-fixer';
        $temp_file = create_tmp_file('fixer');

        $data['error'] =
            'Không thành công! Yêu cầu chạy "composer install"!';

        if (
            $temp_file !== false
            && is_file($fixer_file)
            && function_can_use('exec')
            && file_put_contents($temp_file, $content) !== false
        ) {
            @chmod($fixer_file, 0775);
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
                    $data['format'] = $formatted;
                    $data['error'] = '';
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

            $data['format'] = $result['out'] !== ''
                ? $result['out']
                : $content;
            $data['error'] = $result['err'];

            @unlink($temp_file);
        }

        break;

    default:
        $data['format'] = $content;
        $data['error'] = '';

        break;
}

end_request:
@ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

echo json_encode($data);
