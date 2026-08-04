<?php

define('ACCESS', true);
require __DIR__ . '/_init.php';

if (!function_can_use('exec')) {
    exit('exec() function not found');
}

// cài đặt composer.phar
if (!file_exists('composer.phar')) {
    if (!file_import('composer.phar', 'https://getcomposer.org/download/latest-stable/composer.phar')) {
        exit('Can not install composer.phar!');
    }
}

$site_title = 'Chạy lệnh Composer';
$curr_path = get_curr_path();
check_path($curr_path);

$php = (string) ($_POST['php'] ?? 'php');
$command = (string) ($_POST['command'] ?? 'composer update');

require SITE_HEADER;

echo '<style>
    input[type="text"] {
        width: 100%;
    }

    pre {
        padding: 6px;
        border: 0.5px solid #cecece;
        white-space: pre-wrap;
    }

    pre#output-code {
        color: red;
    }

    pre#output {
        overflow-x: scroll;
        white-space: pre;
        color: green;
    }
</style>';

echo '<div class="title">' . file_print_path($curr_path, true) . '</div>';

echo '<div class="list">';

if (!file_exists($curr_path . '/composer.json')) {
    echo  'Thư mục không có <b>composer.json</b>';
} else {
    echo '<form method="post">
        <span>PHP BINARY:</span><br />
        <input type="text" name="php" value="' . htmlspecialchars((string) $php) . '" /><br />
    
        <span>Lệnh:</span><br />
        <input type="text" name="command" value="' . htmlspecialchars((string) $command) . '" /><br />
    
       <input type="submit" name="submit" value="OK" />
    </form>';
}

echo '</div>';

// OK
if (isset($_POST['submit'])) {
    echo '<div class="title">Kết quả</div>';
    echo '<div class="list">';

    // RUN
    $output = [];
    $result_code = '';
    
    // for composer.phar
    putenv('COMPOSER_HOME=~/.composer');
    
    if (substr($command, 0, 8) === 'composer') {
        $command = substr($command, 8);
        $command = sprintf(
            'cd %s && %s %s %s 2>&1',
            $curr_path,
            $php,
            __DIR__ . '/composer.phar',
            $command
        );
    } else {
        $command = '';
    }

    if ($command) {
        exec($command, $output, $result_code);
    }

    //
    echo 'Result Code:';
    echo '<pre id="output-code">' . htmlspecialchars($result_code) . '</pre>';

    echo 'Kết quả:';
    echo '<pre id="output">' . htmlspecialchars(implode("\n", $output)) . '</pre>';

    echo '</div>';
}

require SITE_FOOTER;
