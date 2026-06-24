<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

$dir = !empty($_GET['dir']) ? rawurldecode($_GET['dir']) : null;
$name = !empty($_GET['name']) ? $_GET['name'] : null;

$site_title = 'Di chuyển thư mục';

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

if ($dir == null || $name == null || !is_dir(process_directory($dir . '/' . $name))) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="' . action_link('index', get_page_list_params()) . '">Danh sách</a></li>
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
            redirect(action_link('index', ['path' => $dir] + get_page_list_params()));

        echo '</div>';
    }

    echo '<div class="list">
        <span class="bull">&bull; </span><span>' . file_print_path($dir . '/' . $name, true) . '</span><hr/>
        <form action="' . action_link('folder_move', ['dir' => $dir, 'name' => $name] + get_page_list_params()) . '" method="post">
            <span class="bull">&bull; </span>Đường dẫn thư mục mới:<br/>
            <input type="text" name="path" value="' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '" size="18"/><br/>
            <input type="submit" name="submit" value="Di chuyển"/>
        </form>
    </div>';

    file_display_actions($dir . '/' . $name);
}

require SITE_FOOTER;
