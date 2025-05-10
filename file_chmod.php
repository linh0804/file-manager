<?php

define('ACCESS', true);

require '.init.php';

$error = '';
$title = 'Chmod tập tin';

check_path($path);

require 'header.php';

echo '<div class="title">' . $title . '</div>';

if (isset($_POST['submit'])) {
    $error .= '<div class="notice_failure">';

    if (empty($_POST['mode']))
        $error .= 'Chưa nhập đầy đủ thông tin';
    else if (!@chmod($path, intval($_POST['mode'], 8)))
        $error .= 'Chmod tập tin thất bại';
    else
        goURL('index.php?path=' . dirname($path) . $pages['paramater_1']);

    $error .= '</div>';
}

echo $error;

echo '<div class="list">
    <span class="bull">&bull;</span><span>' . printPath($path) . '</span><hr/>
    <form action="" method="post">
        <span class="bull">&bull;</span>Chế độ:<br/>
        <input type="text" name="mode" value="' . (isset($_POST['mode']) ? $_POST['mode'] : getChmod($path)) . '" size="18"/><br/>
        <input type="submit" name="submit" value="Chmod"/>
    </form>
</div>';

printFileActions($file);

require 'footer.php';
