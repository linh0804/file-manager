<?php

namespace app;

use ngatngay\http\request;
use ngatngay\zip;
use SplFileInfo;
use ZipArchive;

function get_path()
{
    return (string) request::get('path');
}

function get_entries()
{
    $entries = [];
    $entry = request::post('entry');

    if (is_array($entry)) {
        foreach ($entry as $e) {
            if (empty($e)) {
                continue;
            }

            $entries[] = $e;
        }
    }

    return $entries;
}

function zip_dir($path, $file, $isDelete = false)
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
            remove_dir($path);
        }

        return true;
    }

    return false;
}

function print_actions($filename)
{
    global $pages, $formats, $dirEncode;

    $file = new SplFileInfo($filename);
    $path = $file->getPathname();
    $name = $file->getFilename();
    $ext = $file->getExtension();
    $dir = dirname($path);

    echo '<div class="title">Chức năng</div>';
    echo '<ul class="list">';

    if ($file->isFile()) {
        if (in_array($ext, $formats['zip'])) {
            echo '<li><img src="icon/unzip.png"/> <a href="file_viewzip.php?path=' . $path . $pages['paramater_1'] . '">Xem</a></li>
              <li><img src="icon/unzip.png"/> <a href="file_unzip.php?path=' . $path . $pages['paramater_1'] . '">Giải nén</a></li>';
        } elseif (is_format_text($name) || is_format_unknown($name)) {
            echo '<li><img src="icon/edit.png"/> <a href="edit_text.php?path=' . base64_encode($path) . '">Sửa văn bản</a></li>
              <li><img src="icon/edit_text_line.png"/> <a href="edit_code.php?dir=' . $dir . '&name=' . basename($name) . '">Sửa code</a></li>
              <li><img src="icon/edit_text_line.png"/> <a href="edit_text_line.php?dir=' . $dir . '&name=' . basename($name) . $pages['paramater_1'] . '">Sửa theo dòng</a></li>
              <li><img src="icon/columns.png"/> <a href="view_code.php?dir=' . $dir . '&name=' . basename($name) . '">Xem code</a></li>';
        }
        echo '<li><img src="icon/download.png"/> <a href="download.php?path=' . $path . '">Tải về</a></li>';
    } else {
        echo '<li><img src="icon/zip.png"/> <a href="folder_zip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Nén zip</a></li>';
    }

    echo '<li><img src="icon/rename.png"/> <a href="rename.php?path=' . $path . $pages['paramater_1'] . '">Đổi tên</a></li>';
    echo '<li><img src="icon/copy.png"/> <a href="file.php?act=copy&path=' . $path . $pages['paramater_1'] . '">Sao chép</a></li>';
    echo '<li><img src="icon/move.png"/> <a href="move.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Di chuyển</a></li>';
    echo '<li><img src="icon/access.png"/> <a href="chmod.php?path=' . $path . $pages['paramater_1'] . '">Chmod</a></li>';
    echo '<li><img src="icon/delete.png"/> <a href="delete.php?path=' . $path . $pages['paramater_1'] . '">Xóa</a></li>';

    echo '<li><img src="icon/info.png"/> <a href="file.php?path=' . $path . $pages['paramater_1'] . '">Thông tin</a></li>';
    echo '<li><img src="icon/list.png"/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>';
    echo '</ul>';

    show_back();
}

function t_file_type($filename)
{
    if (is_file($filename)) {
        return 'tập tin';
    }
    if (is_dir($filename)) {
        return 'thư mục';
    }
    return '(unknown)';
}
