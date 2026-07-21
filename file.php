<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$curr_file = new SplFileInfo($curr_path);
$act = $_GET['act'] ?? '';

$site_title = 'File';

check_path($curr_path);

$act = preg_replace('/[^a-z_]/', '', $act);

if (empty($act) || !file_exists(__DIR__ . '/' . 'file_' . $act . '.php')) {
    require SITE_HEADER;

    echo '<div class="title">' . $site_title . '</div>
        <div class="list"><span>Khong co hanh dong</span></div>
        <div class="title">Chuc nang</div>
        <ul class="list">
            <li><img src="icon/list.png" alt=""/> <a href="' . action_link('index', ['path' => $curr_path]) . '">Danh sach</a></li>
        </ul>';

    require SITE_FOOTER;
    exit;
}

require __DIR__ . '/' . 'file_' . $act . '.php';
