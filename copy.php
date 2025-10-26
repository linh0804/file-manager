<?php

namespace app;

use ngatngay\http\request;

define('ACCESS', true);

require '_init.php';

$path = get_path();
check_path($path);

$title = 'Sao chép tập tin';

$dir = dirname($path);
$name = basename($path);

$newName = $_POST['name'] ?? $name;
$newDir = $_POST['dir'] ?? $dir;
$newPath = "$newDir/$newName";
$notice = '';

if (isset($_POST['submit'])) {
    if (empty($newDir) || empty($newName)) {
        $notice = 'Chưa nhập đầy đủ thông tin';
    } elseif (file_exists($newPath)) {
        $notice = 'Tệp đã tồn tại';
    } elseif (!@copy($dir . '/' . $name, $newPath)) {
        $notice = 'Sao chép tập tin thất bại';
    } else {
        redirect('index.php?path=' . $dir . $pages['paramater_1']);
    }
}

require '_header.php';
?>

<div class="title"><?= $title ?></div>

<?= form_err($notice) ?>

<div class="list">
    <span class="bull">&bull;</span><span><?= print_path($dir . '/' . $name) ?></span><hr/>
    <form action="" method="post">
        <span class="bull">&bull;</span>Đường dẫn tập tin mới:<br/>
        <input type="text" name="dir" value="<?= htmlspecialchars((string) $newDir) ?>" size="18"/><br/>
        <input type="text" name="name" value="<?= htmlspecialchars((string) $newName) ?>" size="18"/><br/>
        <input type="submit" name="submit" value="Sao chép"/>
    </form>
</div>

<?php

print_actions($path);

require '_footer.php';
