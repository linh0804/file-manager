<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

$home = $_POST['home'] ?? config()->get('home', '');

if (isset($_POST['submit'])) {
    if ($home) {
        config()->set('home', $home);
    } else {
        config()->unset('home');
    }
    
    redirect(action_link('index'));
}

$site_title = 'Sửa Trang chủ';

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo '<div class="list">';

echo '<form method="post">
    <span>Thư mục:</span><br />
    <input type="text" name="home" value="' . htmlspecialchars((string) $home) . '" /><br />

   <input type="submit" name="submit" value="OK" />
</form>';

echo '</div>';

require SITE_FOOTER;
