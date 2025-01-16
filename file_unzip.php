<?php

define('ACCESS', true);

require '.init.php';

$title = 'Giải nén tập tin';
$format = $name == null ? null : getFormat($name);

require 'header.php';

echo '<div class="title">' . $title . '</div>';

if ($dir == null || $name == null || !is_file(processDirectory($dir . '/' . $name))) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
        </ul>';
} elseif (!in_array($format, array('zip', 'jar'))) {
    echo '<div class="list"><span>Tập tin không phải zip</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
        </ul>';
} else {
    $dir = processDirectory($dir);
    $format = getFormat($name);
    if (isset($_POST['submit'])) {
        echo '<div class="notice_failure">';

        if (empty($_POST['path'])) {
            echo 'Chưa nhập đầy đủ thông tin';
        } elseif (!is_dir(processDirectory($_POST['path']))) {
            echo 'Đường dẫn giải nén không tồn tại';
        } else {
            $zip = new ZipArchive();

            if ($zip->open($dir . '/' . $name) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $fileInfo = $zip->statIndex($i);
                    $filename = $fileInfo['name'];

                    $zip->extractTo(processDirectory($_POST['path']), $filename);
                }
                $zip->close();
                if (isset($_POST['is_delete'])) {
                    @unlink($dir . '/' . $name);
                }

                goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);
            } else {
                echo 'Giải nén tập tin lỗi';
            }
        }

        echo '</div>';
    }

    echo '<div class="list">
        <span class="bull">&bull;</span><span>' . printPath($dir . '/' . $name) . '</span><hr/>
        <form action="file_unzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
            <span class="bull">&bull;</span>Đường dẫn giải nén:<br/>
            <textarea name="path" data-autoresize>' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '</textarea><br/>
            <input type="checkbox" name="is_delete" value="1"' . (isset($_POST['is_delete']) ? ' checked="checked"' : null) . '/> Xóa tập tin zip<br/>
            <input type="submit" name="submit" value="Giải nén"/>
        </form>
    </div>';
    
    printFileActions($file);
}

require 'footer.php';
