<?php

define('ACCESS', true);

require_once '.init.php';

$home = $_POST['home'] ?? cookie('fm_home', '');

if (isset($_POST['submit'])) {
    // save 30 day
    cookie(['fm_home' => $home], [
        'expires' => time() + 86400 * 30
    ]);
    
    goURL('index.php');
}

$title = 'Sửa Trang chủ';

require_once 'header.php';

echo '<div class="title">' . $title . '</div>';

echo '<div class="list">';

echo '<form method="post">
    <span>Thư mục:</span><br />
    <input type="text" name="home" value="' . htmlspecialchars($home) . '" /><br />

   <input type="submit" name="submit" value="OK" />
</form>';

echo '</div>';

require_once 'footer.php';
