<?php
namespace app;

define('ACCESS', true);

require '_init.php';

function chmods($dir, $entrys, $folder, $file)
{
    $folder = intval($folder, 8);
    $file   = intval($file, 8);

    foreach ($entrys as $e) {
        $path = $dir . '/' . $e;

        if (@is_file($path)) {
            if (!@chmod($path, $file)) {
                return false;
            }
        } elseif (@is_dir($path)) {
            if (!@chmod($path, $folder)) {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}

$title = 'Chmod';
$entry = $_POST['entry'] ?? [];

if ($dir == null || !is_dir(process_directory($dir))) {
    require_once '_header.php';

    echo '<div class="title">' . $title . '</div>
        <div class="list"><span>Đường dẫn không tồn tại</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li>
                <img src="icon/list.png" alt="" />
                <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a>
            </li>
        </ul>';
} elseif (!$_POST) {
    require_once '_header.php';

    echo '<div class="title">' . $title . '</div>
        <div class="list"><span>Không có hành động</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
} elseif (count($entry) <= 0) {
    require_once '_header.php';

    echo '<div class="title">' . $title . '</div>
        <div class="list"><span>Không có lựa chọn</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
} else {
    $dir = process_directory($dir);
    $entryCheckbox = '';
    $entryHtmlList = '<ul class="list">';

    foreach ($entry as $e) {
        $isFolder = is_dir($dir . '/' . $e);
        $entryCheckbox .= '<input type="hidden" name="entry[]" value="' . $e . '" checked="checked"/>';
        $entryHtmlList .= '<li>'
            . get_icon($isFolder ? 'folder' : 'file', $e) . ' '
            . ($isFolder ? '<strong class="folder_name">' . $e . '</strong>' : '<span class="file_name">' . $e . '</span>') .
            '</li>';
    }

    $entryHtmlList .= '</ul>';

    require_once '_header.php';

    echo '<div class="title">' . $title . '</div>';

    if (isset($_POST['submit']) && isset($_POST['is_action'])) {
        echo '<div class="notice_failure">';

        if (empty($_POST['folder']) || empty($_POST['file'])) {
            echo 'Chưa nhập đầy đủ thông tin';
        } elseif (!chmods($dir, $entry, $_POST['folder'], $_POST['file'])) {
            echo 'Chmod thất bại';
        } else {
            redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
        }

        echo '</div>';
    }

    echo $entryHtmlList;
    echo '<div class="list">
            <span>' . print_path($dir, true) . '</span><hr/>
            <form action="chmod_multi.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                <span class="bull">&bull; </span>Thư mục:<br/>
                <input type="text" name="folder" value="' . ($_POST['folder'] ?? '755') . '" size="18"/><br/>
                <span class="bull">&bull; </span>Tập tin:<br/>
                <input type="text" name="file" value="' . ($_POST['file'] ?? '644') . '" size="18"/><br/>
                <input type="hidden" name="is_action" value="1"/>';

    echo $entryCheckbox;

    echo '<input type="submit" name="submit" value="Chmod"/>
            </form>
        </div>';

    echo '<div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
}

require_once '_footer.php';
