<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$entries     = $_POST['entries'] ?? [];
$act   = !empty($_GET['act']) ? $_GET['act'] : null;

$site_title = 'Thao tác';

if (empty($curr_path) || !is_dir(process_directory($curr_path))) {
    require SITE_HEADER;

    echo '<div class="title">' . $site_title . '</div>
        <div class="list"><span>Đường dẫn không tồn tại</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li>
                <img src="icon/list.png" alt="" />
                <a href="' . action_link('index', get_page_list_params()) . '">Danh sách</a>
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
            <li><img src="icon/list.png" alt=""/> <a href="' . action_link('index', ['path' => $curr_path] + get_page_list_params()) . '">Danh sách</a></li>
        </ul>';

    require SITE_FOOTER;
    exit;
}

$act = preg_replace('/[^a-z_]/', '', $act);

if ($act === '' || !file_exists(__DIR__ . '/' . 'multi_' . $act . '.php')) {
    require SITE_HEADER;

    echo '<div class="title">' . $site_title . '</div>
        <div class="list"><span>Không có hành động</span></div>
        <div class="title">Chức năng</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="' . action_link('index', ['path' => $curr_path] + get_page_list_params()) . '">Danh sách</a></li>
        </ul>';

    require SITE_FOOTER;
    exit;
}

$curr_path = process_directory($curr_path);

require __DIR__ . '/' . 'multi_' . $act . '.php';
