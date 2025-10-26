<?php
namespace app;

use ngatngay\http\curl;

define('ACCESS', true);

require '_init.php';

if (!isLogin) {
    redirect('login.php');
}

$title = 'Tải lên tập tin';

require '_header.php';

echo '<div class="title">' . $title . '</div>';

if ($dir == null || !is_dir(process_directory($dir))) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
            <div class="title">Chức năng</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
            </ul>';
} else {
    $dir = process_directory($dir);

    if (isset($_POST['submit'])) {
        $isEmpty = true;

        foreach ($_POST['url'] as $entry) {
            if (!empty($entry)) {
                $isEmpty = false;
                break;
            }
        }

        if ($isEmpty) {
            echo '<div class="notice_failure">Chưa nhập url nào cả</div>';
        } else {
            for ($i = 0; $i < count($_POST['url']); ++$i) {
                if (!empty($_POST['url'][$i])) {
                    $curl = new curl();
                    $curl->setFollowLocation();

                    $_POST['url'][$i] = $_POST['url'][$i];

                    if (!is_url($_POST['url'][$i])) {
                        echo '<div class="notice_failure">URL <strong class="url_import">' . $_POST['url'][$i] . '</strong> không hợp lệ</div>';
                    } elseif ($curl->download($_POST['url'][$i], $dir . '/' . basename((string) $_POST['url'][$i]))) {
                        echo '<div class="notice_succeed">Nhập khẩu tập tin <strong class="file_name_import">' . basename((string) $_POST['url'][$i]) . '</strong>, <span class="file_size_import">' . size(filesize($dir . '/' . basename((string) $_POST['url'][$i]))) . '</span> thành công</div>';
                    } else {
                        echo '<div class="notice_failure">Nhập khẩu tập tin <strong class="file_name_import">' . basename((string) $_POST['url'][$i]) . '</strong> thất bại</div>';
                    }
                }
            }
        }
    }

    echo '<div class="list">
        <span>' . print_path($dir, true) . '</span><hr/>
        <form action="import.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
            <span class="bull">&bull; </span>URL 1:<br/>
            <input type="text" name="url[]" size="18"/><br/>
            <span class="bull">&bull; </span>URL:<br/>
            <input type="text" name="url[]" size="18"/><br/>
            <span class="bull">&bull; </span>URL 3:<br/>
            <input type="text" name="url[]" size="18"/><br/>
            <span class="bull">&bull; </span>URL 4:<br/>
            <input type="text" name="url[]" size="18"/><br/>
            <span class="bull">&bull; </span>URL 5:<br/>
            <input type="text" name="url[]" size="18"/><br/>
            <input type="submit" name="submit" value="Nhập khẩu"/>
        </form>
    </div>';

    show_back();
}

require '_footer.php';
