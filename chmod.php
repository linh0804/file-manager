<?php

namespace app;

use ngatngay\http\request;

define('ACCESS', true);

require '_init.php';

$path = get_path();
check_path($path);

$title = 'Chmod tập tin';
$mode = request::post('mode', get_chmod($path));
$error = '';

if (request::is_method('post')) {
    $error .= '<div class="notice_failure">';

    if (empty($mode)) {
        $error .= 'Chưa nhập đầy đủ thông tin';
    } else if (!@chmod($path, intval($mode, 8))) {
        $error .= 'Chmod tập tin thất bại';
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
    <span class="bull">&bull;</span>
    <span><?= print_path($path) ?></span><hr/>
    <form action="" method="post">
        <span class="bull">&bull;</span>
        Chế độ:<br/>
        <input type="text" name="mode" value="<?= $mode ?>" /><br/>
        <input type="submit" name="submit" value="Chmod"/>
    </form>
</div>

<?php

print_actions($file);

require '_footer.php';
