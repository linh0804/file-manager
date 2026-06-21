<?php

use nightmare\http\request;

defined('ACCESS') or exit;

$site_title = 'Di chuyển tập tin';

$message = null;

if (request::has_post('submit')) {
    $requested_path = trim($_POST['path_new'] ?? '');

    if ($requested_path === '') {
        $message = 'Chưa nhập đầy đủ thông tin';
    } else {
        $target_dir = process_directory($requested_path);

        if ($target_dir === dirname($curr_path)) {
            $message = 'Đường dẫn mới phải khác đường dẫn hiện tại';
        } elseif (@rename($curr_path, $target_dir . '/' . basename($curr_path))) {
            redirect(action_link('index', ['path' => dirname($curr_path)] + get_page_list_params()));
        } else {
            $message = 'Di chuyển tập tin thất bại';
        }
    }
}

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

if ($message !== null) {
    echo '<div class="notice_failure">' . $message . '</div>';
}

$default_path = $_POST['path_new'] ?? dirname($curr_path);

echo '<div class="list">
    <span class="bull">&bull; </span><span>' . print_path($curr_path) . '</span><hr/>
    <form action="' . action_link('file', ['act' => 'move', 'path' => $curr_path] + get_page_list_params()) . '" method="post">
        <span class="bull">&bull; </span>Đường dẫn tập tin mới:<br/>
        <input type="text" name="path_new" value="' . htmlspecialchars($default_path, ENT_QUOTES, 'UTF-8') . '"/><br/>
        <input type="submit" name="submit" value="Di chuyển"/>
    </form>
</div>';

file_display_actions($curr_path);

require SITE_FOOTER;
