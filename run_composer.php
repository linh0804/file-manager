<?php
namespace app;

define('ACCESS', true);

$function = 'exec';
if (!function_exists($function)) {
    exit($function . '() function not found');
}

require_once '_init.php';

// cài đặt composer.phar
if (!file_exists('composer.phar')) {
    if (!import('https://getcomposer.org/download/latest-stable/composer.phar', 'composer.phar')) {
        exit('Can not install composer.phar!');
    }
}

$title = 'Chạy lệnh Composer';

require_once '_header.php';

echo '<style>
    input[type="text"] {
        width: 100%;
    }

    pre {
        padding: 6px;
        border: 0.5px solid #cecece;
        white-space: pre-wrap;
    }

    pre#output {
        overflow-x: scroll;
        white-space: pre;
    }
</style>';

echo '<div class="title">' . $title . '</div>';

$folder = $_POST['folder'] ?? $dir;
$php = $_POST['php'] ?? 'php';
$command = $_POST['command'] ?? 'composer update';

echo '<div class="list">';

echo '<form method="post">
    <span>Thư mục:</span><br />
    <input type="text" name="folder" value="' . htmlspecialchars((string) $folder) . '" /><br />

    <span>PHP BINARY:</span><br />
    <input type="text" name="php" value="' . htmlspecialchars((string) $php) . '" /><br />

    <span>Lệnh:</span><br />
    <input type="text" name="command" value="' . htmlspecialchars((string) $command) . '" /><br />

   <input type="submit" name="submit" value="OK" />
</form>';

// OK
if (isset($_POST['submit'])) {
    // RUN
    $output = [];
    $result_code = '';
    
    // for composer.phar
    putenv('COMPOSER_HOME=~/.composer');
    
    if (substr((string) $command, 0, 9) === "composer ") {
        $command = substr((string) $command, 9 - strlen((string) $command));
    }

    $command = sprintf(
        'cd %s && %s %s/composer.phar %s 2>&1',
        processDirectory($folder), $php, rootPath, $command
    );

    if ($command) {
        exec($command, $output, $result_code);
    }

    //
    echo '<hr />';

    echo 'Thư mục:';
    echo '<pre>' . htmlspecialchars((string) $folder) . '</pre>';

    echo 'Lệnh:';
    echo '<pre>' . htmlspecialchars($command) . '</pre>';

    echo 'Code:';
    echo '<pre>' . htmlspecialchars($result_code) . '</pre>';

    echo 'Kết quả:';
    echo '<pre id="output">' . htmlspecialchars(implode("\n", $output)) . '</pre>';
}

echo '</div>';

require_once '_footer.php';
