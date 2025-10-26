<?php
namespace app;

define('ACCESS', true);

require '_init.php';

$title = 'Di chuyển thư mục';

require '_header.php';

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

        if (empty($_POST['path']))
            echo 'Chưa nhập đầy đủ thông tin';
        else if ($dir == process_directory($_POST['path']))
            echo 'Đường dẫn mới phải khác đường dẫn hiện tại';
        else if (!is_dir($_POST['path']))
            echo 'Đường dẫn mới không tồn tại';
        else if (!movedir($dir . '/' . $name, process_directory($_POST['path'])))
            echo 'Di chuyển thư mục thất bại';
        else
            redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);

        echo '</div>';
    }

    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . print_path($dir . '/' . $name, true) . '</span><hr/>
        <form action="folder_move.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
            <span class="bull">&bull; </span>Đường dẫn thư mục mới:<br/>
            <input type="text" name="path" value="' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '" size="18"/><br/>
            <input type="submit" name="submit" value="Di chuyển"/>
        </form>
    </div>';
    
    print_actions($file);
}

require '_footer.php';
