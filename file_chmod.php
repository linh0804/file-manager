<?php

use nightmare\http\request;

defined('ACCESS') or exit;

$site_title = 'Chmod tập tin';
$mode = request::post('mode', file_get_chmod($curr_path));
$error = '';

if (request::is_method('post')) {
    $error .= '<div class="notice_failure">';

    if (empty($mode)) {
        $error .= 'Chưa nhập đầy đủ thông tin';
    } else if (!@chmod($curr_path, intval($mode, 8))) {
        $error .= 'Chmod tập tin thất bại';
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
    <span class="bull">&bull;</span>
    <span><?= print_path($curr_path) ?></span><hr/>
    <form action="" method="post">
        <span class="bull">&bull;</span>
        Chế độ:<br/>
        <input type="text" name="mode" value="<?= $mode ?>" /><br/>
        <input type="submit" name="submit" value="Chmod"/>
    </form>
</div>

<?php

file_display_actions($curr_path);

require SITE_FOOTER;
