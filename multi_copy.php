<?php

define('ACCESS', true);
require __DIR__ . '/multi.php';

$site_title = 'Sao chép';

///

function copys($entrys, $curr_path, $path_new)
{
    foreach ($entrys as $e) {
        $entry_path = $curr_path . '/' . $e;

        if (@is_file($entry_path)) {
            if (!@copy($entry_path, $path_new . '/' . $e)) {
                return false;
            }
        } elseif (@is_dir($entry_path)) {
            if (!copydir($entry_path, $path_new)) {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}

///

if (isset($_POST['submit'])) {
    if (empty($_POST['path_new'])) {
        response_api(['msg' => 'Chưa nhập đầy đủ thông tin']);
    } elseif ($curr_path == process_directory($_POST['path_new'])) {
        response_api(['msg' => 'Đường dẫn mới phải khác đường dẫn hiện tại']);
    } elseif (!is_dir($_POST['path_new'])) {
        response_api(['msg' => 'Đường dẫn mới không tồn tại']);
    } elseif (!copys($entries, $curr_path, process_directory($_POST['path_new']))) {
        response_api(['msg' => 'Sao chép thất bại']);
    } else {
        response_api([
            'status' => true,
            'msg' => 'Thành công',
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
        <span class="bull">&bull; </span>Đường dẫn tập tin mới:<br/>
        <input type="text" name="path_new" value="' . ($_POST['path_new'] ?? $curr_path) . '"/><br/>';

echo $entry_checkbox;

echo '<input type="submit" name="submit" value="Sao chép"/>
    <hr>
    <span style="color: blue">Sẽ ghi đè nếu file tồn tại!<span>
    </form>
</div>';

require SITE_FOOTER;
