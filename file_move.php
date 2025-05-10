<?php

define('ACCESS', true);

require '.init.php';

$title = 'Di chuyển tập tin';

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

        if (empty($_POST['path'])) {
            echo 'Chưa nhập đầy đủ thông tin';
        } elseif ($dir == processDirectory($_POST['path'])) {
            echo 'Đường dẫn mới phải khác đường dẫn hiện tại';
        } elseif (!@rename($dir . '/' . $name, processDirectory($_POST['path']) . '/' . $name)) {
            echo 'Di chuyển tập tin thất bại';
        } else {
            goURL('index.php?path=' . $dirEncode . $pages['paramater_1']);
        }

        echo '</div>';
    }

    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . printPath($dir . '/' . $name) . '</span><hr/>
        <form action="file_move.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
            <span class="bull">&bull; </span>Đường dẫn tập tin mới:<br/>
            <textarea name="path" data-autoresize>' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '</textarea><br/>
            <input type="submit" name="submit" value="Di chuyển"/>
        </form>
    </div>';
    
    printFileActions($file);
}

require 'footer.php';
