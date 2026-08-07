<?php

use Nightmare\Zip;

define('ACCESS', true);
require __DIR__ . '/multi.php';

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

///

$name = $_GET['name'] ?? '';
$curr_path = process_directory($curr_path);
$site_title = 'Nén zip';

if (isset($_POST['submit'])) {
    if (empty($_POST['name']) || empty($_POST['path_new'])) {
        response_api([
            'msg' => 'Chưa nhập đầy đủ thông tin'
        ]);
    } elseif (
        isset($_POST['is_delete'])
        && process_directory($_POST['path_new']) == $curr_path . '/' . ($name ?? '')
    ) {
        response_api([
            'msg' => 'Nếu chọn xóa thư mục bạn không thể lưu tập tin nén ở đó'
        ]);
    } elseif (file_name_valid($_POST['name'])) {
        response_api([
            'msg' => 'Tên tập tin zip không hợp lệ'
        ]);
    } elseif (file_exists(process_directory($_POST['path_new'] . '/' . $_POST['name']))) {
        response_api([
            'msg' => 'Tập tin đã tồn tại, vui lòng đổi tên!'
        ]);
    } elseif (
        !multi_zip(
            $curr_path,
            $entries,
            process_directory($_POST['path_new'] . '/' . $_POST['name']),
            isset($_POST['is_delete'])
        )
    ) {
        response_api([
            'msg' => 'Nén zip thất bại'
        ]);
    } else {
        response_api([
            'status' => true,
            'msg' => 'Thành công',
            'reload' => isset($_POST['is_delete']),
            'reload_after_close' => true
        ]);
    }

    exit;
}

///

require SITE_HEADER;

$entry_checkbox = '';
$entry_html_list = '<ul class="list">';

foreach ($entries as $e) {
    $f = new \SplFileInfo($curr_path . '/' . $e);

    $entry_checkbox .= '<input type="hidden" name="entries[]" value="' . $e . '" checked="checked"/>';
    $entry_html_list .= '<li>' . file_get_display_link($f) . '</li>';
}

$entry_html_list .= '</ul>';

echo '<div class="title">' . $site_title . '</div>';

echo $entry_html_list;

echo '<div class="list">
    <span>' . file_print_path($curr_path, true) . '</span><hr/>

    <form data-ajax action="' . get_curr_url_esc() . '" method="post">
        <span class="bull">&bull; </span>Tên tập tin nén:<br/>
        <input type="text" name="name" value="' . ($_POST['name'] ?? 'archive-' . date('Ymd-His') . '.zip') . '" size="18"/><br/>

        <span class="bull">&bull; </span>Đường dẫn lưu:<br/>
        <input type="text" name="path_new" value="' . ($_POST['path_new'] ?? $curr_path) . '"/><br/>

        <input type="checkbox" name="is_delete" value="1"' . (isset($_POST['is_delete']) ? ' checked="checked"' : null) . '/> Xóa nguồn<br/>';

echo $entry_checkbox;

echo '<input type="submit" name="submit" value="Nén"/>
    </form>
</div>';

require SITE_FOOTER;
