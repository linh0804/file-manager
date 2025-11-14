<?php

namespace app;

use nightmare\http\request;
use SplFileInfo;

define('ACCESS', true);

require '_init.php';

$path = get_path();
check_path($path);

$file = new SplFileInfo($path);
$error = '';
$name = request::post('name', basename($path));
$newPath = dirname($path) . '/' . $name;
$title = 'Đổi tên ' . t_file_type($path);

if (request::has_post('submit')) {    
    $error .= '<div class="notice_failure">';

    if (empty($name)) {
        $error .= 'Chưa nhập đầy đủ thông tin';
    } elseif (is_name_error($name)) {
        $error .= 'Tên tập tin không hợp lệ';
    } elseif (file_exists($newPath)) {
        $error .= 'Tên tập tin đã tồn tại';
    } elseif (!rename($path, $newPath)) {
        $error .= 'Thay đổi thất bại';
    } else {
        redirect('index.php?path=' . dirname($path) . $pages['paramater_1']);
    }

    $error .= '</div>';
}

require '_header.php';

?>

<div class="title"><?= $title ?></div>

<?= $error ?>

<div class="list">
  <span class="bull">&bull;</span><span><?= print_path($path) ?></span><hr/>
  <form method="post">
    <span class="bull">&bull;</span>Tên <?= t_file_type($path) ?>:<br/>
    <input type="text" name="name" value="<?= $name ?>" /><br/>
    <input type="submit" name="submit" value="Thay đổi"/>
  </form>
</div>

<?php
print_actions($path);

require '_footer.php';
