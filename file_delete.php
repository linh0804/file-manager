<?php

use nightmare\fs;
use nightmare\http\request;

defined('ACCESS') or exit;

$error = '';
$site_title = 'Xoá ' . basename($curr_path);

if (request::has_post('submit')) {    
    $error .= '<div class="notice_failure">';
 
    if (!fs::remove($curr_path)) {
        $error .= 'Xoá thất bại';
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
  <form method="post">
    Xác nhận xoá <?= file_print_path($curr_path) ?>!!!<br><br>
    <input type="submit" name="submit" value="Xoá"/>
  </form>
</div>

<?php
file_display_actions($curr_path);

require SITE_FOOTER;
