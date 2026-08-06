<?php

define('ACCESS', true);
require __DIR__ . '/multi.php';

$site_title = 'Xóa';

if (isset($_POST['accept'])) {
    if (!multi_remove($entries, $curr_path)) {
        response_api(['msg' => 'Xóa thất bại']);
    } else {
        response_api([
            'status' => true,
            'msg' => 'Xóa thành công',
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
    <span>Bạn có thực sự muốn xóa các mục đã chọn không?</span><br>

    <form data-ajax action="' . get_curr_url_esc() . '" method="post">'
        . $entry_checkbox
        . '<input type="submit" name="accept" value="Đồng ý"/>
    </form>
</div>';
