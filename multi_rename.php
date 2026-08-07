<?php

define('ACCESS', true);
require __DIR__ . '/multi.php';

$site_title = 'Đổi tên';

function create_temp_rand($curr_path)
{
    do {
        $rand = uniqid('tmp_', true) . bin2hex(random_bytes(16));
    } while (file_exists($curr_path . '/' . $rand));

    return $rand;
}

///

$curr_path = process_directory($curr_path);
$entry_checkbox = '';

foreach ($entries as $e) {
    $entry_checkbox .= '<input type="hidden" name="entries[]" value="' . $e . '" checked="checked"/>';
}

$modifier = $entries;

///

if (isset($_POST['submit'])) {
    $modifier = $_POST['modifier'] ?? [];
    $is_succeed = true;

    if (!is_array($modifier) || count($entries) !== count($modifier)) {
        response_api(['msg' => 'Số lượng tên cũ và tên mới không khớp']);
    }
    
    ///

    foreach ($entries as $name) {
        $entry_path = $curr_path . '/' . $name;

        if (!file_exists($entry_path)) {
            response_api(['msg' => 'File gốc ' . $name . ' không tồn tại!']);
        }

        $temp_path = $curr_path . '/' . create_temp_rand($curr_path);

        if (!@rename($entry_path, $temp_path)) {
            response_api(['msg' => 'File gốc ' . $name . ' không thể đổi tên, hãy kiểm tra lại Owner và Chmod']);
        } else {
            rename($temp_path, $entry_path);
        }
    }

    ///

    $entries_lower = array_map('strtolower', $entries);
    $modifier_lower = array_map('strtolower', $modifier);

    $modifier_unique = array_unique($modifier_lower);
    $modifier_duplicate = array_diff_key($modifier_lower, $modifier_unique);

    foreach ($modifier as $k => $e) {
        $name = $entries[$k];

        if (empty($e)) {
            response_api(['msg' => 'Tên mới của ' . $name . ' trống!']);
        }

        if (file_name_valid($e)) {
            response_api(['msg' => 'File gốc ' . $name . ' có tên mới không hợp quy tắc!']);
        }

        if (!empty($modifier_duplicate)) {
            foreach ($modifier_duplicate as $duplicate_name) {
                response_api(['msg' => 'Tên mới ' . $duplicate_name . ' bị trùng']);
            }
        }

        if (
            file_exists($curr_path . '/' . $e)
            && !in_array($modifier_lower[$k], $entries_lower, true)
        ) {
            response_api(['msg' => 'Tên mới ' . $e . ' đã tồn tại']);
        }
    }
    
    ///

    $rename_mappings = [];

    foreach ($entries as $k => $e) {
        $entry_path = $curr_path . '/' . $e;
        $rand = create_temp_rand($curr_path);
        $rename_mappings[$k] = $rand;

        if (!@rename($entry_path, $curr_path . '/' . $rand)) {
            $is_succeed = false;
        }
    }

    foreach ($entries as $k => $e) {
        $entry_path = $curr_path . '/' . $rename_mappings[$k];

        if (!@rename($entry_path, $curr_path . '/' . $modifier[$k])) {
            $is_succeed = false;
        }
    }
    
    ///

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
