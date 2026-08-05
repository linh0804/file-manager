<?php

define('ACCESS', true);
require __DIR__ . '/file.php';

$site_title = 'Tạo mới';

if (isset($_POST['submit'])) {
    $error = '';
    $newDir = $curr_path . '/' . $_POST['name'];
    
    $error .= '<div class="notice_failure">';

    if (empty($_POST['name'])) {
        $error .= 'Chưa nhập đầy đủ thông tin';
    } else if (file_exists($newDir)) {
        $error .= 'Tên đã tồn tại dạng thư mục hoặc tập tin';
    } else if (file_name_valid($_POST['name'])) {
        $error .= 'Tên không hợp lệ';
    } else {
        if (intval($_POST['type']) === 0) {
            if (!@mkdir($newDir))
                $error .= 'Tạo thư mục thất bại';
            else
                redirect(act_link('index', ['path' => $curr_path]));
        } else if (intval($_POST['type']) === 1) {
            if (@file_put_contents($newDir, '') === false)
                $error .= 'Tạo tập tin thất bại';
            else
                redirect(act_link('index', ['path' => $curr_path]));
        } else {
            $error .= 'Lựa chọn không hợp lệ';
        }
    }

    $error .= '</div>';
    echo $error;
    exit;
}


echo '<div class="title">' . $site_title . '</div>';

echo '<div class="list">
    <span>' . file_print_path($curr_path, true) . '</span><hr/>
    <form action="" method="post">
        <span class="bull">&bull; </span>Tên:<br/>

        <input type="text" name="name" value="" /><br/>

        <button name="type" value="1" class="button">
            <img src="icon/file.png" alt=""/>
            Tập tin
        </button>

        <button name="type" value="0" class="button">
            <img src="icon/folder.png" alt=""/>
            Thư mục
        </button>

        <input type="hidden" name="submit" value="1" />      
    </form>
</div>';
