<?php define('ACCESS', true);

    include_once '.init.php';

    if (isLogin) {
        $title = 'Chmod thư mục';

        include_once 'header.php';

        echo '<div class="title">' . $title . '</div>';

        if ($dir == null || $name == null || !is_dir(processDirectory($dir . '/' . $name))) {
            echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
            <div class="title">Chức năng</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
            </ul>';
        } else {
            $dir = processDirectory($dir);

            if (isset($_POST['submit'])) {
                echo '<div class="notice_failure">';

                if (empty($_POST['mode']))
                    echo 'Chưa nhập đầy đủ thông tin';
                else if (!@chmod($dir . '/' . $name, intval($_POST['mode'], 8)))
                    echo 'Chmod thư mục thất bại';
                else
                    goURL('index.php?path=' . $dirEncode . $pages['paramater_1']);

                echo '</div>';
            }

            echo '<div class="list">
                <span class="bull">&bull; </span><span>' . printPath($dir . '/' . $name, true) . '</span><hr/>
                <form action="folder_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
                    <span class="bull">&bull; </span>Chế độ:<br/>
                    <input type="text" name="mode" value="' . getChmod($dir . '/' . $name) . '" size="18"/><br/>
                    <input type="submit" name="submit" value="Chmod"/>
                </form>
            </div>';

            printFolderActions();
        }

        include_once 'footer.php';
    } else {
        goURL('login.php');
    }

?>