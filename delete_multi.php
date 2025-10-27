<?php
namespace app;

define('ACCESS', true);

require '_init.php';

$title = 'Xóa';
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

    if (isset($_POST['accept'])) {
        if (!rrms($entry, $dir)) {
            echo '<div class="notice_failure">Xóa thất bại</div>';
        } else {
            redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
        }
    } elseif (isset($_POST['not_accept'])) {
        redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
    }

    echo $entryHtmlList;
    echo '<div class="list">
            <span>' . print_path($dir, true) . '</span><hr/>
            <span>Bạn có thực sự muốn xóa các mục đã chọn không?</span><hr/><br/>
            <center>
                <form action="delete_multi.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
                    <input type="hidden" name="is_action" value="1"/>';

    echo $entryCheckbox;

    echo '<input type="submit" name="accept" value="Đồng ý"/>
                    <input type="submit" name="not_accept" value="Huỷ bỏ"/>
                </form>
            </center>
        </div>';

    echo '<div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
}

require_once '_footer.php';
