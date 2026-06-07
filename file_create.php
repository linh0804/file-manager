<?php

defined('ACCESS') or exit;

$site_title = 'Tạo mới';
$error = '';

if (isset($_POST['submit'])) {
    $newDir = $curr_path . '/' . $_POST['name'];
    
    $error .= '<div class="notice_failure">';

    if (empty($_POST['name'])) {
        $error .= 'Chưa nhập đầy đủ thông tin';
    } else if (file_exists($newDir)) {
        $error .= 'Tên đã tồn tại dạng thư mục hoặc tập tin';
    } else if (is_name_error($_POST['name'])) {
        $error .= 'Tên không hợp lệ';
    } else {
        if (intval($_POST['type']) === 0) {
            if (!@mkdir($newDir))
                $error .= 'Tạo thư mục thất bại';
            else
                redirect(action_link('index', ['path' => $curr_path] + get_page_list_params()));
        } else if (intval($_POST['type']) === 1) {
            if (@file_put_contents($newDir, '') === false)
                $error .= 'Tạo tập tin thất bại';
            else
                redirect(action_link('index', ['path' => $curr_path] + get_page_list_params()));
        } else {
            $error .= 'Lựa chọn không hợp lệ';
        }
    }

    $error .= '</div>';
}

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo $error;

echo '<div class="list">
    <span>' . print_path($curr_path, true) . '</span><hr/>
    <form action="" method="post">
        <span class="bull">&bull; </span>Tên:<br/>
        <input type="text" name="name" value="' . ($_POST['name'] ?? null) . '" size="18"/><br/>
        <button name="type" value="1" class="button"><img src="icon/file.png" alt=""/> Tập tin</button>
        <button name="type" value="0" class="button"><img src="icon/folder.png" alt=""/> Thư mục </button>
        <input type="hidden" name="submit" value="1" />
    </form>
</div>';
?>

<?php
show_back();

require SITE_FOOTER;
