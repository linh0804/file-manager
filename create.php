<?php

const ACCESS = true;

require '.init.php';

$title = 'Tạo mới';

require 'header.php';

echo '<div class="title">' . $title . '</div>';

if (!$file->isDir()) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
      <li><img src="icon/list.png" alt=""/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
    </ul>';
} else {
    $dir = processDirectory($dir);

    if (isset($_POST['submit'])) {
        $newDir = $dir . '/' . $_POST['name'];
        
        echo '<div class="notice_failure">';

        if (empty($_POST['name'])) {
            echo 'Chưa nhập đầy đủ thông tin';
        } else if (file_exists($newDir)) {
            echo 'Tên đã tồn tại dạng thư mục hoặc tập tin';
        } else if (isNameError($_POST['name'])) {
            echo 'Tên không hợp lệ';
        } else {
            if (intval($_POST['type']) === 0) {
                if (!@mkdir($newDir))
                    echo 'Tạo thư mục thất bại';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            } else if (intval($_POST['type']) === 1) {
                if (@file_put_contents($newDir, '') === false)
                    echo 'Tạo tập tin thất bại';
                else
                    goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            } else {
                echo 'Lựa chọn không hợp lệ';
            }
        }

        echo '</div>';
    }

    echo '<div class="list">
        <span>' . printPath($dir, true) . '</span><hr/>
        <form action="create.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post">
            <span class="bull">&bull; </span>Tên:<br/>
            <input type="text" name="name" value="' . ($_POST['name'] ?? null) . '" size="18"/><br/>
            <button name="type" value="1" class="button"><img src="icon/file.png" alt=""/> Tập tin</button>
            <button name="type" value="0" class="button"><img src="icon/folder.png" alt=""/> Thư mục </button>
            <input type="hidden" name="submit" value="1" />
        </form>
    </div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/upload.png" alt=""/> <a href="upload.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Tải lên tập tin</a></li>
        <li><img src="icon/import.png" alt=""/> <a href="import.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Nhập khẩu tập tin</a></li>
        <li><img src="icon/list.png" alt=""/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
    </ul>';
}

require 'footer.php';


