<?php

use nightmare\fs;

defined('ACCESS') or exit;

$file = new SplFileInfo($curr_path);
$site_title = 'Thông tin ' . basename($curr_path);

$format = file_get_ext(basename($curr_path));
$is_image = false;
$pixel = null;

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo '<ul class="info">';
echo '<li class="not_ellipsis"><span class="bull">&bull; </span><strong>Đường dẫn</strong>: <span>' . file_print_path($file, $file->isDir() ? true : false) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Tên</strong>: <span>' . basename((string) $file) . '</span></li>';

if ($file->isFile()) {
    if ($format && in_array($format, array('png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'))) {
        $pixel = getimagesize($curr_path);
        $is_image = true;

        echo '<li><center><img src="' . action_link('file', ['act' => 'download_image', 'path' => $curr_path]) . '" width="' . ($pixel[0] > 200 ? 200 : $pixel[0]) . 'px"/></center><br/></li>';
    }

    echo '<li><span class="bull">&bull; </span><strong>Kích thước</strong>: <span>' . fs::readable_size($file->getSize()) . '</span></li>';

    if ($is_image) {
        echo '<li><span class="bull">&bull; </span><strong>Độ phân giải</strong>: <span>' . $pixel[0] . 'x' . $pixel[1] . '</span></li>';
    }
}

echo '<li><span class="bull">&bull; </span><strong>Owner</strong>: <span>' . (posix_getpwuid($file->getOwner())['name']) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Chmod</strong>: <span>' . file_get_chmod($file) . '</span></li>';

echo '<li><span class="bull">&bull; </span><strong>Ngày tạo</strong>: <span>' . date('d.m.Y - H:i:s', $file->getCTime()) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Ngày sửa</strong>: <span>' . date('d.m.Y - H:i:s', $file->getMTime()) . '</span></li>';

echo '</ul>';

file_display_actions($curr_path);

require SITE_FOOTER;
