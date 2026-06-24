<?php

use nightmare\http\request;

defined('ACCESS') or exit;

$error = '';
$name = request::post('name', basename($curr_path));
$newPath = dirname($curr_path) . '/' . $name;
$site_title = 'Đổi tên ' . t_file_type($curr_path);

if (request::has_post('submit')) {    
    $error .= '<div class="notice_failure">';

    if (empty($name)) {
        $error .= 'Chưa nhập đầy đủ thông tin';
    } elseif (file_name_valid($name)) {
        $error .= 'Tên tập tin không hợp lệ';
    } elseif (file_exists($newPath)) {
        $error .= 'Tên tập tin đã tồn tại';
    } elseif (!rename($curr_path, $newPath)) {
        $error .= 'Thay đổi thất bại';
    } else {
        redirect(action_link('index', ['path' => dirname($curr_path)] + get_page_list_params()));
    }

    $error .= '</div>';
}

require SITE_HEADER;

?>

<div class="title"><?= $site_title ?></div>

<?= $error ?>

<div class="list">
  <span class="bull">&bull;</span><span><?= print_path($curr_path) ?></span><hr/>
  <form method="post">
    <span class="bull">&bull;</span>Tên <?= t_file_type($curr_path) ?>:<br/>
    <input type="text" name="name" value="<?= $name ?>" /><br/>
    <input type="submit" name="submit" value="Thay đổi"/>
  </form>
</div>

<?php
file_display_actions($curr_path);

require SITE_FOOTER;
