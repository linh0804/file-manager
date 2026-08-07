<?php

define('ACCESS', true);
require __DIR__ . '/multi.php';

$site_title = 'Đổi tên';
$curr_path = process_directory($curr_path);
$entry_checkbox = '';

foreach ($entries as $e) {
    $entry_checkbox .= '<input type="hidden" name="entries[]" value="' . $e . '" checked="checked"/>';
}

$modifier = $entries;

if (isset($_POST['submit'])) {
    $modifier  = $_POST['modifier'];
    $is_succeed = true;

    foreach ($modifier as $k => $e) {
        $entry_path = $curr_path . '/' . $entries[$k];

        if (empty($e)) {
            response_api(['msg' => 'Không được để trống ô nào']);
        } elseif (file_name_valid($e)) {
            $entry_label = is_dir($entry_path) ? 'thư mục' : 'tập tin';

            response_api(['msg' => 'Tên ' . $entry_label . ' ' . $entries[$k] . ' => ' . $e . ' không hợp lệ']);
        } elseif (count_string_array($modifier, strtolower((string) $e), true) > 1 && $e != $entries[$k]) {
            $entry_label = is_dir($entry_path) ? 'thư mục' : 'tập tin';

            response_api(['msg' => 'Tên ' . $entry_label . ' ' . $entries[$k] . ' => ' . $e . ' này đã tồn tại ở một khung nhập khác']);
        } elseif (!is_in_array($entries, strtolower((string) $e), true) && file_exists($curr_path . '/' . $e)) {
            $entry_label = is_dir($entry_path) ? 'thư mục' : 'tập tin';

            response_api(['msg' => 'Tên ' . $entry_label . ' ' . $entries[$k] . ' => ' . $e . ' này đã tồn tại']);
        }
    }

    $rand = md5(rand(1000, 99999) . '-' . $curr_path);
    $rand = substr($rand, 0, strlen($rand) >> 1);

    foreach ($entries as $e) {
        $entry_path = $curr_path . '/' . $e;

        @rename($entry_path, $entry_path . '-' . $rand);
    }

    foreach ($entries as $k => $e) {
        $entry_path = $curr_path . '/' . $e;

        if (!@rename($entry_path . '-' . $rand, $curr_path . '/' . $modifier[$k])) {
            $is_succeed = false;
        } else {
            $entries[$k] = $modifier[$k];
        }
    }

    if (!$is_succeed) {
        response_api(['msg' => 'Đổi tên thất bại']);
    }

    response_api([
        'status' => true,
        'msg' => 'Thành công',
        'reload' => true
    ]);
}

echo '<div class="title">' . $site_title . '</div>';

echo '<div class="list break-word">
        <span>' . file_print_path($curr_path, true) . '</span><hr/>
        <form data-ajax action="' . get_curr_url_esc() . '" method="post">';

for ($i = 0; $i < count($entries); ++$i) {
    $entry_path = $curr_path . '/' . $entries[$i];
    $entry_name = $entries[$i];

    echo '<img src="icon/' . (is_dir($entry_path) ? 'folder' : 'file') . '.png" style="margin-bottom: -3px"> ' . $entry_name . '<br>';
    echo '<input type="text" name="modifier[]" value="' . $modifier[$i] . '" size="18"/><hr/>';
}

echo $entry_checkbox;

echo '<input type="submit" name="submit" value="Đổi tên"/>
        </form>
    </div>';
