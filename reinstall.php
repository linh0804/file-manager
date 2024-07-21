<?php

define('ACCESS', true);
define('alwaysCheckUpdate', true);

require_once 'function.php';

$title = 'Cài đặt lại Manager!!!';

require_once 'header.php';

echo '<div class="title">' . $title . '</div>';

$remoteVersion = getNewVersion();

if ($remoteVersion === false) {
    echo '<div class="list">Lỗi máy chủ cập nhật!</div>';
} else {
    if (isset($_POST['submit'])) {
        $file = 'tmp/manager-reinstall.zip';

        if (import(REMOTE_FILE, $file)) {
            $zip = new ZipArchive;

            if ($zip->open($file) === true) {
                $zip->extractTo('tmp/');
                $zip->close();

                @unlink($file);

                $folder = 'tmp/' . $remoteVersion['repo'];

                mergeFolder($folder, __DIR__);

                removeDir($folder);

                echo '<div class="list">Cài đặt lại thành công</div>';
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
}

require_once 'footer.php';
