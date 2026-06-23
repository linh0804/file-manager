<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

function movedir($old, $new, $isParent = true)
{
    $handler = @scandir($old);

    if ($handler !== false) {
        if ($isParent && $old != '/') {
            $s   = explode('/', (string) $old);
            $end = $new = $new . '/' . end($s);

            if (@is_file($end) || (!@is_dir($end) && !@mkdir($end))) {
                return false;
            }
        } elseif (!$isParent && !@is_dir($new) && !@mkdir($new)) {
            return false;
        }

        foreach ($handler as $entry) {
            if ($entry != '.' && $entry != '..') {
                $paOld = $old . '/' . $entry;
                $paNew = $new . '/' . $entry;

                if (@is_file($paOld)) {
                    if (!@rename($paOld, $paNew)) {
                        return false;
                    }
                } elseif (@is_dir($paOld)) {
                    if (!movedir($paOld, $paNew, false)) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }

        return @rmdir($old);
    }

    return false;
}

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
        <span class="bull">&bull; </span><span>' . print_path($dir . '/' . $name, true) . '</span><hr/>
        <form action="' . action_link('folder_move', ['dir' => $dir, 'name' => $name] + get_page_list_params()) . '" method="post">
            <span class="bull">&bull; </span>Đường dẫn thư mục mới:<br/>
            <input type="text" name="path" value="' . (isset($_POST['path']) ? $_POST['path'] : $dir) . '" size="18"/><br/>
            <input type="submit" name="submit" value="Di chuyển"/>
        </form>
    </div>';

    $file = new SplFileInfo($dir . '/' . $name);
    file_display_actions($file);
}

require SITE_FOOTER;
