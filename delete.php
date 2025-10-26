<?php

namespace app;

use ngatngay\fs;
use ngatngay\http\request;
use SplFileInfo;

define('ACCESS', true);

require '_init.php';

$path = get_path();
check_path($path);

$error = '';
$title = 'Xoá ' . t_file_type($path);

if (request::has_post('submit')) {    
    $error .= '<div class="notice_failure">';
 
    if (!fs::remove($path)) {
        $error .= 'Xoá thất bại';
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
  <form method="post">
    Xác nhận xoá <?= print_path($path) ?>!!!<br><br>
    <input type="submit" name="submit" value="Xoá"/>
  </form>
</div>

<?php
print_actions($path);

require '_footer.php';
