<?php

define('ACCESS', true);
require __DIR__ . '/multi.php';

$site_title = 'Chmod';

///

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

///

$curr_path = process_directory($curr_path);

if (isset($_POST['submit'])) {
    if (empty($_POST['folder']) || empty($_POST['file'])) {
        response_api(['msg' => 'Chưa nhập đầy đủ thông tin']);
    } elseif (!chmods($curr_path, $entries, $_POST['folder'], $_POST['file'])) {
        response_api(['msg' => 'Chmod thất bại']);
    } else {
        response_api([
            'status' => true,
            'msg' => 'Thành công',
            'reload' => true
        ]);
    }

    exit;
}

///

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
            <span class="bull">&bull; </span>Thư mục:<br/>
            <input type="text" name="folder" value="' . ($_POST['folder'] ?? '755') . '" size="18"/><br/>
            <span class="bull">&bull; </span>Tập tin:<br/>
            <input type="text" name="file" value="' . ($_POST['file'] ?? '644') . '" size="18"/><br/>';

echo $entry_checkbox;

echo '<input type="submit" name="submit" value="Chmod"/>
    </form>
</div>';
