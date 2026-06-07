<?php

function chmods($curr_path, $entrys, $folder, $file)
{
    $folder = intval($folder, 8);
    $file   = intval($file, 8);

    foreach ($entrys as $e) {
        $entry_path = $curr_path . '/' . $e;

        if (@is_file($entry_path)) {
            if (!@chmod($entry_path, $file)) {
                return false;
            }
        } elseif (@is_dir($entry_path)) {
            if (!@chmod($entry_path, $folder)) {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}

$site_title = 'Chmod';
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

    if (empty($_POST['folder']) || empty($_POST['file'])) {
        echo 'Chưa nhập đầy đủ thông tin';
    } elseif (!chmods($curr_path, $entries, $_POST['folder'], $_POST['file'])) {
        echo 'Chmod thất bại';
    } else {
        redirect(action_link('index', ['path' => $curr_path] + get_page_list_params()));
    }

    echo '</div>';
}

echo $entry_html_list;
echo '<div class="list">
        <span>' . print_path($curr_path, true) . '</span><hr/>
        <form action="' . action_link('multi', ['act' => 'chmod', 'path' => $curr_path] + get_page_list_params()) . '" method="post">
            <span class="bull">&bull; </span>Thư mục:<br/>
            <input type="text" name="folder" value="' . ($_POST['folder'] ?? '755') . '" size="18"/><br/>
            <span class="bull">&bull; </span>Tập tin:<br/>
            <input type="text" name="file" value="' . ($_POST['file'] ?? '644') . '" size="18"/><br/>
            <input type="hidden" name="is_action" value="1"/>';

echo $entry_checkbox;

echo '<input type="submit" name="submit" value="Chmod"/>
        </form>
    </div>';

echo '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png" alt=""/> <a href="' . action_link('index', ['path' => $curr_path] + get_page_list_params()) . '">Danh sách</a></li>
    </ul>';

require SITE_FOOTER;
