<?php
namespace app;

define('ACCESS', true);

require '_init.php';

$home = $_POST['home'] ?? config()->get('home', '');

if (isset($_POST['submit'])) {
    if ($home) {
        config()->set('home', $home);
    } else {
        config()->remove('home');
    }
    
    redirect('index.php');
}

$title = 'Sửa Trang chủ';

require '_header.php';

echo '<div class="title">' . $title . '</div>';

echo '<div class="list">';

echo '<form method="post">
    <span>Thư mục:</span><br />
    <input type="text" name="home" value="' . htmlspecialchars((string) $home) . '" /><br />

   <input type="submit" name="submit" value="OK" />
</form>';

echo '</div>';

require '_footer.php';
