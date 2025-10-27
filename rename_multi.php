<?php
namespace app;

define('ACCESS', true);

require '_init.php';

$title = 'Đổi tên';
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

    foreach ($entry as $e) {
        $entryCheckbox .= '<input type="hidden" name="entry[]" value="' . $e . '" checked="checked"/>';
    }

    $modifier = $entry;

    require_once '_header.php';

    echo '<div class="title">' . $title . '</div>';

    if (isset($_POST['submit']) && isset($_POST['is_action'])) {
        $modifier  = $_POST['modifier'];
        $isFailed  = false;
        $isSucceed = true;

        foreach ($modifier as $k => $e) {
            $entryPath = $dir . '/' . $entry[$k];

            if (empty($e)) {
                $isFailed = true;

                echo '<div class="notice_failure">Không được để trống ô nào</div>';
                break;
            } elseif (is_name_error($e)) {
                $isFailed   = true;
                $entryLabel = is_dir($entryPath) ? 'thư mục' : 'tập tin';
                $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                echo '<div class="notice_failure">Tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $entry[$k] . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> không hợp lệ</div>';
                break;
            } elseif (count_string_array($modifier, strtolower((string) $e), true) > 1 && $e != $entry[$k]) {
                $isFailed   = true;
                $entryLabel = is_dir($entryPath) ? 'thư mục' : 'tập tin';
                $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                echo '<div class="notice_failure">Tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $entry[$k] . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> này đã tồn tại ở một khung nhập khác</div>';
                break;
            } elseif (!is_in_array($entry, strtolower((string) $e), true) && file_exists($dir . '/' . $e)) {
                $isFailed   = true;
                $entryLabel = is_dir($entryPath) ? 'thư mục' : 'tập tin';
                $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                echo '<div class="notice_failure">Tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $entry[$k] . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> này đã tồn tại</div>';
                break;
            }
        }

        if (!$isFailed) {
            $isSucceed = true;
            $rand      = md5(rand(1000, 99999) . '-' . $dir);
            $rand      = substr($rand, 0, strlen($rand) >> 1);

            foreach ($entry as $e) {
                $entryPath = $dir . '/' . $e;

                @rename($entryPath, $entryPath . '-' . $rand);
            }

            foreach ($entry as $k => $e) {
                $entryPath  = $dir . '/' . $e;
                $entryLabel = is_dir($entryPath) ? 'thư mục' : 'tập tin';
                $entryCss   = is_dir($entryPath) ? 'folder' : 'file';

                if (!@rename($entryPath . '-' . $rand, $dir . '/' . process_name($modifier[$k]))) {
                    $isSucceed = false;

                    echo '<div class="notice_failure">Đổi tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $modifier[$k] . '</strong> thất bại</div>';
                } else {
                    $entry[$k] = $modifier[$k];

                    echo '<div class="notice_succeed">Đổi tên ' . $entryLabel . ' <strong class="' . $entryCss . '_name_rename_action">' . $e . '</strong> <strong>=></strong> <strong class="' . $entryCss . '_name_rename_action">' . $modifier[$k] . '</strong> thành công</div>';
                }
            }
        }

        if (!$isFailed && $isSucceed) {
            redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
        }
    }

    echo '<div class="list break-word">
            <span>' . print_path($dir, true) . '</span><hr/>
            <form action="rename_multi.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">';

    for ($i = 0; $i < count($entry); ++$i) {
        $entryPath = $dir . '/' . $entry[$i];
        $entryName = $entry[$i];

        if (is_dir($entryPath)) {
            echo '<span class="bull">&bull; </span>Tên thư mục (<strong class="folder_name_rename_action">' . $entryName . '</strong>):<br/>';
        } else {
            echo '<span class="bull">&bull; </span>Tên tập tin (<strong class="file_name_rename_action">' . $entryName . '</strong>):<br/>';
        }

        echo '<input type="text" name="modifier[]" value="' . $modifier[$i] . '" size="18"/><br/>';
    }

    echo '<input type="hidden" name="is_action" value="1"/>';

    echo $entryCheckbox;

    echo '<input type="submit" name="submit" value="Đổi tên"/>
            </form>
        </div>';

    echo '<div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
}

require_once '_footer.php';
