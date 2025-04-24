<?php

define('ACCESS', true);

require '.init.php';

$title = 'Cài đặt lại Manager!!!';

require 'header.php';

echo '<div class="title">' . $title . '</div>';

if (isset($_POST['submit'])) {
    $file = 'tmp/manager-reinstall.zip';

    if (import(remoteFile, $file)) {
        $zip = new ZipArchive;

        if ($zip->open($file) === true) {
            $zip->extractTo(__DIR__);
            $zip->close();

            @unlink($file);

            goURL('index.php');
        } else {
            echo '<div class="list">Lỗi</div>';
        }
    } else {
        echo '<div class="list">Lỗi! Không thể tải bản  cập nhật</div>';
    }
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
