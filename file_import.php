<?php

use nightmare\fs;

defined('ACCESS') or exit;

$site_title = 'Tải lên tập tin';

if (isset($_POST['submit'])) {
    $is_empty = true;

    foreach ($_POST['url'] as $entry) {
        if (!empty($entry)) {
            $is_empty = false;
            break;
        }
    }

    if ($is_empty) {
        echo '<div class="notice_failure">Chưa nhập url nào cả</div>';
    } else {
        for ($i = 0; $i < count($_POST['url']); ++$i) {
            if (!empty($_POST['url'][$i])) {
                if (!is_url($_POST['url'][$i])) {
                    echo '<div class="notice_failure">URL <strong class="url_import">' . $_POST['url'][$i] . '</strong> không hợp lệ</div>';
                } elseif (file_import($curr_path . '/' . basename((string) $_POST['url'][$i]), $_POST['url'][$i])) {
                    echo '<div class="notice_succeed">Nhập khẩu tập tin <strong class="file_name_import">' . basename((string) $_POST['url'][$i]) . '</strong>, <span class="file_size_import">' . fs::readable_size(filesize($curr_path . '/' . basename((string) $_POST['url'][$i]))) . '</span> thành công</div>';
                } else {
                    echo '<div class="notice_failure">Nhập khẩu tập tin <strong class="file_name_import">' . basename((string) $_POST['url'][$i]) . '</strong> thất bại</div>';
                }
            }
        }
    }
}

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo '<div class="list">
    <span>' . file_print_path($curr_path, true) . '</span><hr/>
    <form action="" method="post">
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

require SITE_FOOTER;
