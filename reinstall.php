<?php

namespace app;

use ngatngay\zip;

define('ACCESS', true);

require '.init.php';

$title = 'Cài đặt lại Manager!!!';
$error = '';

if (isset($_POST['submit'])) {
    $file = 'tmp/manager-reinstall.zip';

    if (import(remoteFile, $file)) {
        $zip = new zip;

        if ($zip->open($file) === true) {
            $zip->extractTo(__DIR__);
            $zip->close();

            @unlink($file);

            goURL('index.php');
        } else {
            $error = '<div class="list">Lỗi</div>';
        }
    } else {
        $error = '<div class="list">Lỗi! Không thể tải bản  cập nhật</div>';
    }
}

require 'header.php';

echo '<div class="title">' . $title . '</div>';

if ($error) {
    echo $error;
} else {
    echo '<div class="list">
        <span>Cài đặt lại Manager? Bạn phải tự chịu rủi ro khi thực hiện thao tác này!!!</span><hr />
        <form method="post">
            <input type="hidden" name="token" value="' . time() . '" />
            <input type="submit" name="submit" value="Xác nhận!!!"/>
        </form>
    </div>';
}

require 'footer.php';
