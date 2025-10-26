<?php
namespace app;

define('ACCESS', true);

require_once '_init.php';


$title = 'Nén zip thư mục';

require_once '_header.php';

echo '<div class="title">' . $title . '</div>';

if ($dir == null || $name == null || !is_dir(process_directory($dir . '/' . $name))) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
    </ul>';
} else {
    $dir = process_directory($dir);

    if (isset($_POST['submit'])) {
        echo '<div class="notice_failure">';

        if (empty($_POST['name']) || empty($_POST['path'])) {
            echo 'Chưa nhập đầy đủ thông tin';
        } elseif (isset($_POST['is_delete']) && process_directory($_POST['path']) == $dir . '/' . $name) {
            echo 'Nếu chọn xóa thư mục bạn không thể lưu tập tin nén ở đó';
        } elseif (is_name_error($_POST['name'])) {
            echo 'Tên tập tin zip không hợp lệ';
        } elseif (file_exists(process_directory($_POST['path'] . '/' . process_name($_POST['name'])))) {
            echo 'Tập tin đã tồn tại, vui lòng đổi tên!';
        } elseif (!zip_dir($dir . '/' . $name, process_directory($_POST['path'] . '/' . process_name($_POST['name'])), isset($_POST['is_delete']) == 1)) {
            echo 'Nén zip thư mục thất bại';
        } else {
            redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
        }

        echo '</div>';
    }

    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . print_path($dir . '/' . $name, true) . '</span><hr/>
        <form action="folder_zip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
            <span class="bull">&bull; </span>Tên tập tin nén:<br/>
            <input type="text" name="name" value="' . (isset($_POST['name']) ? $_POST['name'] : $name . '.zip') . '" size="18"/><br/>
            <span class="bull">&bull; </span>Đường dẫn lưu:<br/>
            <input type="text" name="path" value="' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '" size="18"/><br/>
            <input type="checkbox" name="is_delete" value="1"/> Xóa thư mục<br/>
            <input type="submit" name="submit" value="Nén"/>
        </form>
    </div>';
    
    print_actions($dir . '/' . $name);
}

require_once '_footer.php';
