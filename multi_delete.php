<?php

$site_title = 'Xóa';
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

if (isset($_POST['accept'])) {
    if (!multi_remove($entries, $curr_path)) {
        echo '<div class="notice_failure">Xóa thất bại</div>';
    } else {
        redirect(action_link('index', ['path' => $curr_path]));
    }
} elseif (isset($_POST['not_accept'])) {
    redirect(action_link('index', ['path' => $curr_path]));
}

echo $entry_html_list;
echo '<div class="list">
        <span>' . file_print_path($curr_path, true) . '</span><hr/>
        <span>Bạn có thực sự muốn xóa các mục đã chọn không?</span><hr/><br/>
        <center>
            <form action="" method="post">
                <input type="hidden" name="is_action" value="1"/>';

echo $entry_checkbox;

echo '<input type="submit" name="accept" value="Đồng ý"/>
                <input type="submit" name="not_accept" value="Huỷ bỏ"/>
            </form>
        </center>
    </div>';

echo '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png" alt=""/> <a href="' . action_link('index', ['path' => $curr_path]) . '">Danh sách</a></li>
    </ul>';

require SITE_FOOTER;
