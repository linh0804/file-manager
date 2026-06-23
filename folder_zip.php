<?php

use nightmare\http\request;
use nightmare\config;
use nightmare\fs;
use nightmare\http\curl;
use nightmare\http\http;
use nightmare\zip;

define('ACCESS', true);
require __DIR__ . '/_init.php';

function folder_zip($path, $file, $isDelete = false)
{
    if (@is_file($file)) {
        @unlink($file);
    }

    $zip = new zip();

    if ($zip->open($file, ZipArchive::CREATE) === true) {
        $path = realpath($path);
        $files = read_full_dir($path);

        foreach ($files as $name => $file) {
            $filePath = $file->getRealPath();
            $zip->add($filePath, $path . DIRECTORY_SEPARATOR);
        }

        $zip->close();

        if ($isDelete) {
            fs::remove($path);
        }

        return true;
    }

    return false;
}

$dir = !empty($_GET['dir']) ? rawurldecode($_GET['dir']) : null;
$name = !empty($_GET['name']) ? $_GET['name'] : null;
$site_title = 'Nén thư mục';

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

if ($dir == null || $name == null || !is_dir(process_directory($dir . '/' . $name))) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="' . action_link('index', get_page_list_params()) . '">Danh sách</a></li>
    </ul>';
} else {
    $dir = process_directory($dir);

    if (isset($_POST['submit'])) {
        echo '<div class="notice_failure">';

        if (empty($_POST['name']) || empty($_POST['path'])) {
            echo 'Chưa nhập đầy đủ thông tin';
        } elseif (isset($_POST['is_delete']) && process_directory($_POST['path']) == $dir . '/' . $name) {
            echo 'Nếu chọn xóa thư mục bạn không thể lưu tập tin nén ở đó';
        } elseif (file_name_valid($_POST['name'])) {
            echo 'Tên tập tin zip không hợp lệ';
        } elseif (file_exists(process_directory($_POST['path'] . '/' . process_name($_POST['name'])))) {
            echo 'Tập tin đã tồn tại, vui lòng đổi tên!';
        } elseif (!folder_zip($dir . '/' . $name, process_directory($_POST['path'] . '/' . process_name($_POST['name'])), isset($_POST['is_delete']) == 1)) {
            echo 'Nén zip thư mục thất bại';
        } else {
            redirect(action_link('index', ['path' => $dir] + get_page_list_params()));
        }

        echo '</div>';
    }

    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . print_path($dir . '/' . $name, true) . '</span><hr/>
        <form action="' . action_link('folder_zip', ['dir' => $dir, 'name' => $name] + get_page_list_params()) . '" method="post">
            <span class="bull">&bull; </span>Tên tập tin nén:<br/>
            <input type="text" name="name" value="' . (isset($_POST['name']) ? $_POST['name'] : $name . '.zip') . '" size="18"/><br/>
            <span class="bull">&bull; </span>Đường dẫn lưu:<br/>
            <input type="text" name="path" value="' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '" size="18"/><br/>
            <input type="checkbox" name="is_delete" value="1"/> Xóa thư mục<br/>
            <input type="submit" name="submit" value="Nén"/>
        </form>
    </div>';
    
    file_display_actions($dir . '/' . $name);
}

require SITE_FOOTER;
