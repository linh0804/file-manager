<?php

$site_title = 'Đổi tên';
$curr_path = process_directory($curr_path);
$entry_checkbox = '';

foreach ($entries as $e) {
    $entry_checkbox .= '<input type="hidden" name="entries[]" value="' . $e . '" checked="checked"/>';
}

$modifier = $entries;

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

if (isset($_POST['submit']) && isset($_POST['is_action'])) {
    $modifier  = $_POST['modifier'];
    $is_failed  = false;
    $is_succeed = true;

    foreach ($modifier as $k => $e) {
        $entry_path = $curr_path . '/' . $entries[$k];

        if (empty($e)) {
            $is_failed = true;

            echo '<div class="notice_failure">Không được để trống ô nào</div>';
            break;
        } elseif (file_name_valid($e)) {
            $is_failed   = true;
            $entry_label = is_dir($entry_path) ? 'thư mục' : 'tập tin';
            $entry_css   = is_dir($entry_path) ? 'folder' : 'file';

            echo '<div class="notice_failure">Tên ' . $entry_label . ' <strong class="' . $entry_css . '_name_rename_action">' . $entries[$k] . '</strong> <strong>=></strong> <strong class="' . $entry_css . '_name_rename_action">' . $e . '</strong> không hợp lệ</div>';
            break;
        } elseif (count_string_array($modifier, strtolower((string) $e), true) > 1 && $e != $entries[$k]) {
            $is_failed   = true;
            $entry_label = is_dir($entry_path) ? 'thư mục' : 'tập tin';
            $entry_css   = is_dir($entry_path) ? 'folder' : 'file';

            echo '<div class="notice_failure">Tên ' . $entry_label . ' <strong class="' . $entry_css . '_name_rename_action">' . $entries[$k] . '</strong> <strong>=></strong> <strong class="' . $entry_css . '_name_rename_action">' . $e . '</strong> này đã tồn tại ở một khung nhập khác</div>';
            break;
        } elseif (!is_in_array($entries, strtolower((string) $e), true) && file_exists($curr_path . '/' . $e)) {
            $is_failed   = true;
            $entry_label = is_dir($entry_path) ? 'thư mục' : 'tập tin';
            $entry_css   = is_dir($entry_path) ? 'folder' : 'file';

            echo '<div class="notice_failure">Tên ' . $entry_label . ' <strong class="' . $entry_css . '_name_rename_action">' . $entries[$k] . '</strong> <strong>=></strong> <strong class="' . $entry_css . '_name_rename_action">' . $e . '</strong> này đã tồn tại</div>';
            break;
        }
    }

    if (!$is_failed) {
        $is_succeed = true;
        $rand      = md5(rand(1000, 99999) . '-' . $curr_path);
        $rand      = substr($rand, 0, strlen($rand) >> 1);

        foreach ($entries as $e) {
            $entry_path = $curr_path . '/' . $e;

            @rename($entry_path, $entry_path . '-' . $rand);
        }

        foreach ($entries as $k => $e) {
            $entry_path  = $curr_path . '/' . $e;
            $entry_label = is_dir($entry_path) ? 'thư mục' : 'tập tin';
            $entry_css   = is_dir($entry_path) ? 'folder' : 'file';

            if (!@rename($entry_path . '-' . $rand, $curr_path . '/' . $modifier[$k])) {
                $is_succeed = false;

                echo '<div class="notice_failure">Đổi tên ' . $entry_label . ' <strong class="' . $entry_css . '_name_rename_action">' . $e . '</strong> <strong>=></strong> <strong class="' . $entry_css . '_name_rename_action">' . $modifier[$k] . '</strong> thất bại</div>';
            } else {
                $entries[$k] = $modifier[$k];

                echo '<div class="notice_succeed">Đổi tên ' . $entry_label . ' <strong class="' . $entry_css . '_name_rename_action">' . $e . '</strong> <strong>=></strong> <strong class="' . $entry_css . '_name_rename_action">' . $modifier[$k] . '</strong> thành công</div>';
            }
        }
    }

    if (!$is_failed && $is_succeed) {
        redirect(action_link('index', ['path' => $curr_path] + get_page_list_params()));
    }
}

echo '<div class="list break-word">
        <span>' . file_print_path($curr_path, true) . '</span><hr/>
        <form action="" method="post">';

for ($i = 0; $i < count($entries); ++$i) {
    $entry_path = $curr_path . '/' . $entries[$i];
    $entry_name = $entries[$i];

    echo '<img src="icon/' . (is_dir($entry_path) ? 'folder' : 'file') . '.png" style="margin-bottom: -3px"> ' . $entry_name . '<br>';
    echo '<input type="text" name="modifier[]" value="' . $modifier[$i] . '" size="18"/><hr/>';
}

echo '<input type="hidden" name="is_action" value="1"/>';

echo $entry_checkbox;

echo '<input type="submit" name="submit" value="Đổi tên"/>
        </form>
    </div>';

echo '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png" alt=""/> <a href="' . action_link('index', ['path' => $curr_path] + get_page_list_params()) . '">Danh sách</a></li>
    </ul>';

require SITE_FOOTER;
