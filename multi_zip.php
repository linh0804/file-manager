<?php

use nightmare\zip;

function multi_zip($dir, $entrys, $file, $isDelete = false)
{
    if (@is_file($file)) {
        @unlink($file);
    }

    $zip = new Zip();
    if ($zip->open($file, ZipArchive::CREATE) !== true) {
        return false;
    }
    foreach ($entrys as $entry) {
        $path = "$dir/$entry";
        $zip->add($path, $dir);

        if (is_dir($path)) {
            $files = read_full_dir($path);

            foreach ($files as $value) {
                $zip->add($value->getPathname(), $dir);
            }
        }
    }
    $zip->close();

    if ($isDelete) {
        multi_remove($entrys, $dir);
    }

    return true;
}

$name = !empty($_GET['name']) ? $_GET['name'] : null;

$site_title = 'Nén zip';
$curr_path = process_directory($curr_path);
$entry_checkbox = '';
$entry_html_list = '<ul class="list">';

foreach ($entries as $e) {
    $f = new \SplFileInfo($curr_path . '/' . $e);

    $entry_checkbox .= '<input type="hidden" name="entries[]" value="' . $e . '" checked="checked"/>';
    $entry_html_list .= '<li>' . file_get_display_link($f) . '</li>';
}

$entry_html_list .= '</ul>';

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

if (isset($_POST['submit']) && isset($_POST['is_action'])) {
    echo '<div class="notice_failure">';

    if (empty($_POST['name']) || empty($_POST['path_new'])) {
        echo 'Chưa nhập đầy đủ thông tin';
    } elseif (isset($_POST['is_delete']) && process_directory($_POST['path_new']) == $curr_path . '/' . ($name ?? '')) {
        echo 'Nếu chọn xóa thư mục bạn không thể lưu tập tin nén ở đó';
    } elseif (file_name_valid($_POST['name'])) {
        echo 'Tên tập tin zip không hợp lệ';
    } elseif (file_exists(process_directory($_POST['path_new'] . '/' . $_POST['name']))) {
        echo 'Tập tin đã tồn tại, vui lòng đổi tên!';
    } elseif (!multi_zip($curr_path, $entries, process_directory($_POST['path_new'] . '/' . $_POST['name']), isset($_POST['is_delete']))) {
        echo 'Nén zip thất bại';
    } else {
        redirect(action_link('index', ['path' => $curr_path] + get_page_list_params()));
    }

    echo '</div>';
}

echo $entry_html_list;
echo '<div class="list">
        <span>' . file_print_path($curr_path, true) . '</span><hr/>
        <form action="' . action_link('multi', ['act' => 'zip', 'path' => $curr_path] + get_page_list_params()) . '" method="post">
            <span class="bull">&bull; </span>Tên tập tin nén:<br/>
            <input type="text" name="name" value="' . ($_POST['name'] ?? 'archive.zip') . '" size="18"/><br/>
            <span class="bull">&bull; </span>Đường dẫn lưu:<br/>
            <input type="text" name="path_new" value="' . ($_POST['path_new'] ?? $curr_path) . '"/><br/>
            <input type="checkbox" name="is_delete" value="1"' . (isset($_POST['is_delete']) ? ' checked="checked"' : null) . '/> Xóa nguồn<br/>
            <input type="hidden" name="is_action" value="1"/>';

echo $entry_checkbox;

echo '<input type="submit" name="submit" value="Nén"/>
        </form>
    </div>';

echo '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png" alt=""/> <a href="' . action_link('index', ['path' => $curr_path] + get_page_list_params()) . '">Danh sách</a></li>
    </ul>';

require SITE_FOOTER;
