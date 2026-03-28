<?php


use nightmare\http\request;
use nightmare\zip;

defined('ACCESS') or exit('Not access');

function encode_path($path)
{
    return base64_encode($path);
}
function decode_path($path)
{
    //$path =
    $path = str_replace('\\', '/', $path);
}

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


function is_path_not_permission($path, $isUseName = false): bool
{
    if (!empty($path)) {
        $reg  = $isUseName ? NAME_DIRECTORY_INSTALL_FILE_MANAGER : PATH_FILE_MANAGER;
        $reg  = $reg != null ? strtolower($reg) : null;
        $path = str_replace('\\', '/', $path);
        $path = strtolower($path);

        if (preg_match('#^' . $reg . '$#si', $path)) {
            return true;
        } elseif (preg_match('#^' . $reg . '/(^\/+|^\\+)(.*?)$#si', $path)) {
            return true;
        } elseif (preg_match('#^' . $reg . '/(.*?)$#si', $path)) {
            return true;
        }

        return false;
    }

    return false;
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
            echo '<li><img src="icon/unzip.png"/> <a href="zip_view.php?path=' . $path . $pages['paramater_1'] . '">Xem</a></li>
              <li><img src="icon/unzip.png"/> <a href="unzip.php?path=' . $path . $pages['paramater_1'] . '">Giải nén</a></li>';
        } elseif (is_format_text($name) || is_format_unknown($name)) {
            echo '<li><img src="icon/edit.png"/> <a href="edit_text.php?path=' . base64_encode($path) . '">Sửa văn bản</a></li>
              <li><img src="icon/edit_text_line.png"/> <a href="edit_code.php?path=' . $path . '">Sửa code</a></li>
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

function file_get_display_link($file)
{
    global $formats, $pages;

    $path = $file->getPathname();
    $file_dir = $file->isDir() ? $file->getPathname() : dirname($file->getPathname());
    $name = $file->getFilename();
    $is_edit = false;

    $file_icon = get_icon($file->isDir() ? 'folder' : 'file', $name);

    if ($file->isFile()) {
        if (in_array($file->getExtension(), $formats['text'])) {
            $is_edit = true;
        } elseif (in_array(
            strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name),
            $formats['source']
        )) {
            $is_edit = true;
        } elseif (is_format_unknown($name)) {
            $is_edit = true;
        }

        if (strtolower($file->getFilename()) === 'error_log' || $is_edit) {
            $file_link = 'edit_text.php?path=' . base64_encode($file->getPathname());
        } elseif (in_array($file->getExtension(), $formats['zip'])) {
            $file_link = 'unzip.php?path=' . $file->getPathname() . $pages['paramater_1'];
        } else {
            $file_link = 'rename.php?path=' . $path . $pages['paramater_1'];
        }
    } else {
        $file_link = 'rename.php?path=' . $path . $pages['paramater_1'];
    }

    $file_icon = sprintf('<a href="%s">%s</a>', $file_link, $file_icon);

    if (is_app_dir($path)) {
        $name_display = '<span style="color: red !important">' . $name . '</span>';
    } else {
        $name_display = $name;
    }

    if ($file->isLink()) {
        $name_display = '<span style="color:darkcyan">' . $name_display . '</span>';
    }

    return sprintf(
        '%s <a href="%s">%s</a>',
        $file_icon,
        $file->isDir() ? 'index.php?path=' . $file_dir : 'file.php?path=' . $path,
        $name_display
    );
}
