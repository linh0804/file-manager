<?php

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

$site_title = 'Sao chép';
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

    if (empty($_POST['path_new'])) {
        echo 'Chưa nhập đầy đủ thông tin';
    } elseif ($curr_path == process_directory($_POST['path_new'])) {
        echo 'Đường dẫn mới phải khác đường dẫn hiện tại';
    } elseif (!is_dir($_POST['path_new'])) {
        echo 'Đường dẫn mới không tồn tại';
    } elseif (!copys($entries, $curr_path, process_directory($_POST['path_new']))) {
        echo 'Sao chép thất bại';
    } else {
        redirect(action_link('index', ['path' => $curr_path] + get_page_list_params()));
    }

    echo '</div>';
}

echo $entry_html_list;
echo '<div class="list">
        <span>' . file_print_path($curr_path, true) . '</span><hr/>
        <form action="' . action_link('multi', ['act' => 'copy', 'path' => $curr_path] + get_page_list_params()) . '" method="post">
            <span class="bull">&bull; </span>Đường dẫn tập tin mới:<br/>
            <input type="text" name="path_new" value="' . ($_POST['path_new'] ?? $curr_path) . '"/><br/>
            <input type="hidden" name="is_action" value="1"/>';

echo $entry_checkbox;

echo '<input type="submit" name="submit" value="Sao chép"/>
        </form>
    </div>';

echo '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png" alt=""/> <a href="' . action_link('index', ['path' => $curr_path] + get_page_list_params()) . '">Danh sách</a></li>
    </ul>';

require SITE_FOOTER;
