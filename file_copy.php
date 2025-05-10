<?php

define('ACCESS', true);

require '.init.php';

$title = 'Sao chép tập tin';

require 'header.php';

echo '<div class="title">' . $title . '</div>';

if (!$file->isFile()) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
    </ul>';
} else {
    $dir = processDirectory($dir);
    
    $newName = $_POST['name'] ?? $name;
    $newDir = $_POST['dir'] ?? $dir;
    $newPath = "$newDir/$newName";
 
    if (isset($_POST['submit'])) {        
        echo '<div class="notice_failure">';

        if (empty($newDir) || empty($newName)) {
            echo 'Chưa nhập đầy đủ thông tin';
        } elseif (file_exists($newPath)) {
            echo 'Tệp đã tồn tại';
        } elseif (!@copy($dir . '/' . $name, $newPath)) {
            echo 'Sao chép tập tin thất bại';
        } else {
            goURL('index.php?path=' . $dirEncode . $pages['paramater_1']);
        }

        echo '</div>';
    }

    echo '<div class="list">
        <span class="bull">&bull;</span><span>' . printPath($dir . '/' . $name) . '</span><hr/>
        <form action="file_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
            <span class="bull">&bull;</span>Đường dẫn tập tin mới:<br/>
            <input type="text" name="dir" value="' . htmlspecialchars($newDir) . '" size="18"/><br/>
            <input type="text" name="name" value="' . htmlspecialchars($newName) . '" size="18"/><br/>
            <input type="submit" name="submit" value="Sao chép"/>
        </form>
    </div>';

    printFileActions($file);
}

require 'footer.php';
