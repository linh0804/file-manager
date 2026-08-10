<?php

defined('ACCESS') or exit;

require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$entries = $_POST['entries'] ?? [];
$site_title = 'Thao tác';

if (empty($curr_path) || !is_dir(process_directory($curr_path))) {
    require SITE_HEADER;

    echo '<div class="title">' . $site_title . '</div>
        <div class="list"><span>Đường dẫn không tồn tại</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li>
                <img src="icon/list.png" alt="" />
                <a href="' . act_link('index') . '">Danh sách</a>
            </li>
        </ul>';

    require SITE_FOOTER;
    exit;
}

if (count($entries) <= 0) {
    require SITE_HEADER;

    echo '<div class="title">' . $site_title . '</div>
        <div class="list"><span>Không có lựa chọn</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="' . act_link('index', ['path' => $curr_path]) . '">Danh sách</a></li>
        </ul>';

    require SITE_FOOTER;
    exit;
}

$curr_path = process_directory($curr_path);
