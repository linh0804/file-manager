<?php

use NgatNgay\FS;

define('ACCESS', true);

require '.init.php';

$title = 'Xóa tập tin';

require 'header.php';

echo '<div class="title">' . $title . '</div>';

if ($dir == null || $name == null || !file_exists(processDirectory($dir . '/' . $name))) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
    </ul>';
} else {
    $dir = processDirectory($dir);
    $format = getFormat($name);

    if (isset($_POST['accept'])) {
        if (!FS::remove($dir . '/' . $name))
            echo '<div class="notice_failure">Xóa tập tin thất bại</div>';
        else
            goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
    } else if (isset($_POST['not_accept'])) {
        goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
    }

    echo '<div class="list">
        <span>Bạn có thực sự muốn xóa ' . ($file->isFile() ? 'tập tin' : 'thư mục') . ' <strong class="file_name_delete">' . $name . '</strong> không?</span><hr/>
        <span>Đường dẫn: ' . printPath($dir . '/' . $name) . '</span><hr/>
        <center>
            <form action="file_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
                <input type="submit" name="accept" value="Đồng ý"/>
                <input type="submit" name="not_accept" value="Huỷ bỏ"/>
            </form>
        </center>
    </div>';
    
    showBack();
}

require 'footer.php';
 