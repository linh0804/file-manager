<?php

define('ACCESS', true);
require __DIR__ . '/file.php';

$site_title = 'Tạo mới';

if (isset($_POST['submit'])) {
    $new_dir = $curr_path . '/' . $_POST['name'];

    if (empty($_POST['name'])) {
        response_api(['msg' => 'Chưa nhập đầy đủ thông tin']);
    } else if (file_exists($new_dir)) {
        response_api(['msg' => 'Tên đã tồn tại dạng thư mục hoặc tập tin']);
    } else if (file_name_valid($_POST['name'])) {
        response_api(['msg' => 'Tên không hợp lệ']);
    } else {
        if (intval($_POST['type']) === 0) {
            if (!@mkdir($new_dir)) {
                response_api(['msg' => 'Tạo thư mục thất bại']);
            } else {
                response_api([
                    'status' => true,
                    'msg' => 'Tạo thư mục thành công',
                    'redirect' => act_link('index', ['path' => $curr_path])
                ]);
            }
        } else if (intval($_POST['type']) === 1) {
            if (@file_put_contents($new_dir, '') === false) {
                response_api(['msg' => 'Tạo tập tin thất bại']);
            } else {
                response_api([
                    'status' => true,
                    'msg' => 'Tạo tập tin thành công',
                    'redirect' => act_link('index', ['path' => $curr_path])
                ]);
            }
        } else {
            response_api(['msg' => 'Lựa chọn không hợp lệ']);
        }
    }

    exit;
}


echo '<div class="title">' . $site_title . '</div>';

echo '<div class="list">
    <span>' . file_print_path($curr_path, true) . '</span><hr/>

    <form data-ajax action="' . get_curr_url_esc() . '" method="post">
        <span class="bull">&bull; </span>Tên:<br/>

        <input type="text" name="name" value="" /><br/>

        <button type="submit" name="type" value="1" class="button">
            <img src="icon/file.png" alt=""/>
            Tập tin
        </button>

        <button type="submit" name="type" value="0" class="button">
            <img src="icon/folder.png" alt=""/>
            Thư mục
        </button>

        <input type="hidden" name="submit" value="1" />      
    </form>
</div>';
