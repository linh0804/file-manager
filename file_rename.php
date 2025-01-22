<?php

define('ACCESS', true);

require '.init.php';

$title = 'Đổi tên tập tin';

require 'header.php';

echo '<div class="title">' . $title . '</div>';

if ($dir == null || $name == null || !is_file(processDirectory($dir . '/' . $name))) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
    </ul>';
} else {
    $dir = processDirectory($dir);
    $format = getFormat($name);

    if (isset($_POST['submit'])) {
        echo '<div class="notice_failure">';

        if (empty($_POST['name']))
            echo 'Chưa nhập đầy đủ thông tin';
        else if (isNameError($_POST['name']))
            echo 'Tên tập tin không hợp lệ';
        else if (!@rename($dir . '/' . $name, $dir . '/' . $_POST['name']))
            echo 'Thay đổi thất bại';
        else
            goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);

        echo '</div>';
    }

    echo '<div class="list">
        <span class="bull">&bull;</span><span>' . printPath($dir . '/' . $name) . '</span><hr/>
        <form action="file_rename.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
            <span class="bull">&bull;</span>Tên tập tin:<br/>
            <input type="text" name="name" value="' . (isset($_POST['name']) ? $_POST['name'] : $name) . '" size="18"/><br/>
            <input type="submit" name="submit" value="Thay đổi"/>
        </form>
    </div>';
    
    printFileActions($file);
}

require 'footer.php';

?>