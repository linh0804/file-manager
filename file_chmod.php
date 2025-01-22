<?php define('ACCESS', true);

require '.init.php';

$title = 'Chmod tập tin';

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

        if (empty($_POST['mode']))
            echo 'Chưa nhập đầy đủ thông tin';
        else if (!@chmod($dir . '/' . $name, intval($_POST['mode'], 8)))
            echo 'Chmod tập tin thất bại';
        else
            goURL('index.php?dir=' . $dirEncode . $pages['paramater_1']);

        echo '</div>';
    }

    echo '<div class="list">
        <span class="bull">&bull;</span><span>' . printPath($dir . '/' . $name) . '</span><hr/>
        <form action="file_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
            <span class="bull">&bull;</span>Chế độ:<br/>
            <input type="text" name="mode" value="' . (isset($_POST['mode']) ? $_POST['mode'] : getChmod($dir . '/' . $name)) . '" size="18"/><br/>
            <input type="submit" name="submit" value="Chmod"/>
        </form>
    </div>';

    printFileActions($file);
}

require 'footer.php';

?>