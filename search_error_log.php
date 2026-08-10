<?php

use Nightmare\Fs;

define('ACCESS', true);
require __DIR__ . '/_init.php';

$site_title = 'Tìm error_log';
$curr_path = get_curr_path();
check_path($curr_path);

$error_log = (string) ($_POST['error_log'] ?? 'error_log');

if (!empty($_POST)) {
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(
            $curr_path,
            FilesystemIterator::SKIP_DOTS
        )
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getFilename() === $error_log) {
            echo '<li>'
                . '<a href="#">' . $file->getPathname() . '</a>'
                . ' (' . Fs::sizen($file->getSize()) . ')'
            . '</li>';
        }
    }

    exit;
}

require SITE_HEADER;

echo '<style>
    ul#result li {
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
        overflow-wrap: anywhere;
    }
    
    ul#result li a {
        color: red !important;
    }
</style>';

echo '<div class="title">' . file_print_path($curr_path, true) . '</div>';

echo '<div class="list">
    <form id="my-form" method="post">
        <span>Tên file error_log:</span><br />
        <input type="text" name="error_log" value="' . htmlspecialchars($error_log) . '" /><br />
    
       <input type="submit" name="submit" value="OK" />
    </form>
</div>';

echo '<div class="title">Kết quả</div>';
echo '<ul id="result" class="list"><li>(trống)</li></ul>';

echo '<script>
$(function () {
$(document).on("submit", "#my-form", function (event) {
    event.preventDefault();

    $.ajax({
        method: "POST",
        url: window.location.href,
        data: $(this).serialize(),
        success: function (res) {
            $("#result").html(res);
        }
    });
});
});
</script>';

require SITE_FOOTER;
