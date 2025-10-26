<?php
namespace app;

define('ACCESS', true);

require '_init.php';

$title = 'Di chuyển tập tin';

require '_header.php';

echo '<div class="title">' . $title . '</div>';

$processedDir = $dir ? process_directory($dir) : null;
$sourcePath = $processedDir ? $processedDir . '/' . $name : null;

if ($processedDir === null || $name === null || !is_file($sourcePath)) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
            <div class="title">Chức năng</div>
            <ul class="list">
                <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
            </ul>';

    require '_footer.php';
    return;
}

$message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect input once to avoid duplicate sanitisation work.
    $requestedPath = trim($_POST['path'] ?? '');

    if ($requestedPath === '') {
        $message = 'Chưa nhập đầy đủ thông tin';
    } else {
        $targetDir = process_directory($requestedPath);

        if ($targetDir === $processedDir) {
            $message = 'Đường dẫn mới phải khác đường dẫn hiện tại';
        } elseif (@rename($sourcePath, $targetDir . '/' . $name)) {
            redirect('index.php?path=' . $dirEncode . $pages['paramater_1']);
        } else {
            $message = 'Di chuyển tập tin thất bại';
        }
    }
}

if ($message !== null) {
    echo '<div class="notice_failure">' . $message . '</div>';
}

$defaultPath = $_POST['path'] ?? $processedDir;

echo '<div class="list">
    <span class="bull">&bull; </span><span>' . print_path($sourcePath) . '</span><hr/>
    <form action="move.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" method="post">
        <span class="bull">&bull; </span>Đường dẫn tập tin mới:<br/>
        <textarea name="path" data-autoresize>' . htmlspecialchars($defaultPath, ENT_QUOTES, 'UTF-8') . '</textarea><br/>
        <input type="submit" name="submit" value="Di chuyển"/>
    </form>
</div>';

print_actions($file);

require '_footer.php';
