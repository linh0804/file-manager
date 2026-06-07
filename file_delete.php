<?php

use nightmare\fs;
use nightmare\http\request;

defined('ACCESS') or exit;

$error = '';
$site_title = 'Xoá ' . t_file_type($curr_path);

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
    Xác nhận xoá <?= print_path($curr_path) ?>!!!<br><br>
    <input type="submit" name="submit" value="Xoá"/>
  </form>
</div>

<?php
print_actions($curr_path);

require SITE_FOOTER;
