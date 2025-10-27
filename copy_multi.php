<?php
namespace app;

define('ACCESS', true);

require '_init.php';

function copys($entrys, $dir, $path)
{
    foreach ($entrys as $e) {
        $pa = $dir . '/' . $e;

        if (@is_file($pa)) {
            if (!@copy($pa, $path . '/' . $e)) {
                return false;
            }
        } elseif (@is_dir($pa)) {
            if (!copydir($pa, $path)) {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}

$title = 'Sao chép';
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

        if (empty($_POST['path'])) {
            echo 'Chưa nhập đầy đủ thông tin';
        } elseif ($dir == process_directory($_POST['path'])) {
            echo 'Đường dẫn mới phải khác đường dẫn hiện tại';
        } elseif (!is_dir($_POST['path'])) {
            echo 'Đường dẫn mới không tồn tại';
        } elseif (!copys($entry, $dir, process_directory($_POST['path']))) {
            echo 'Sao chép thất bại';
        } else {
            redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
        }

        echo '</div>';
    }

    echo $entryHtmlList;
    echo '<div class="list">
            <span>' . print_path($dir, true) . '</span><hr/>
            <form action="copy_multi.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                <span class="bull">&bull; </span>Đường dẫn tập tin mới:<br/>
                <textarea name="path" data-autoresize>' . ($_POST['path'] ?? $dir) . '</textarea><br/>
                <input type="hidden" name="is_action" value="1"/>';

    echo $entryCheckbox;

    echo '<input type="submit" name="submit" value="Sao chép"/>
            </form>
        </div>';

    echo '<div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
}

require_once '_footer.php';
