<?php

define('ACCESS', true);
require __DIR__ . '/file.php';

$name = (string) ($_POST['name'] ?? basename($curr_path));
$new_path = dirname($curr_path) . '/' . $name;
$site_title = 'Đổi tên ' . basename($curr_path);

if (isset($_POST['submit'])) {
    if (empty($name)) {
        response_api(['msg' => 'Chưa nhập đầy đủ thông tin']);
    } else if (file_name_valid($name)) {
        response_api(['msg' => 'Tên tập tin không hợp lệ']);
    } else if (file_exists($new_path)) {
        response_api(['msg' => 'Tên tập tin đã tồn tại']);
    } else if (!@rename($curr_path, $new_path)) {
        response_api(['msg' => 'Thay đổi thất bại']);
    } else {
        response_api([
            'status' => true,
            'msg' => 'Đổi tên thành công',
            'redirect' => act_link('index', ['path' => dirname($curr_path)])
        ]);
    }

    exit;
}

echo '<div class="title">' . $site_title . '</div>';

echo '<div class="list">
    <span class="bull">&bull;</span><span>' . file_print_path($curr_path) . '</span><hr/>

    <form data-ajax action="' . get_curr_url_esc() . '" method="post">
        <span class="bull">&bull; </span>Tên:<br/>
        <input type="text" name="name" value="' . htmlspecialchars($name) . '" /><br/>

        <input type="submit" name="submit" value="Thay đổi" />
    </form>
</div>';
