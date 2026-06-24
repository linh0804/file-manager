<?php

use nightmare\fs;

defined('ACCESS') or exit;

$file = new SplFileInfo($curr_path);
$site_title = 'Thông tin ' . t_file_type($curr_path);

$format = file_get_ext(basename($curr_path));
$isImage = false;
$pixel = null;

$dir_size = 0;
$total_file = 0;
$total_dir = 0;

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo '<ul class="info">';
echo '<li class="not_ellipsis"><span class="bull">&bull; </span><strong>Đường dẫn</strong>: <span>' . print_path($file, $file->isDir() ? true : false) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Tên</strong>: <span>' . basename((string) $file) . '</span></li>';

if ($file->isFile()) {
    if ($format && in_array($format, array('png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'))) {
        $pixel = getimagesize($curr_path);
        $isImage = true;

        echo '<li><center><img src="' . action_link('file', ['act' => 'download_image', 'path' => $curr_path]) . '" width="' . ($pixel[0] > 200 ? 200 : $pixel[0]) . 'px"/></center><br/></li>';
    }

    echo '<li><span class="bull">&bull; </span><strong>Kích thước</strong>: <span>' . fs::readable_size($file->getSize()) . '</span></li>';

    if ($isImage) {
        echo '<li><span class="bull">&bull; </span><strong>Độ phân giải</strong>: <span>' . $pixel[0] . 'x' . $pixel[1] . '</span></li>';
    }
}

if ($file->isDir()) {
    echo '<li><span class="bull">&bull; </span><strong>Kích thước thư mục</strong>: <span>' . fs::readable_size(filesize($file)) . '</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Dung lượng thư mục</strong>: <span>' . fs::readable_size($dir_size) . ' (' . $dir_size . ' byte)</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Tổng số thư mục</strong>: <span>' . $total_dir . '</span></li>';    
    echo '<li><span class="bull">&bull; </span><strong>Tổng số file</strong>: <span>' . $total_file . '</span></li>';
}

echo '<li><span class="bull">&bull; </span><strong>Owner</strong>: <span>' . (posix_getpwuid($file->getOwner())['name']) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Chmod</strong>: <span>' . file_get_chmod($file) . '</span></li>';

echo '<li><span class="bull">&bull; </span><strong>Ngày tạo</strong>: <span>' . date('d.m.Y - H:i:s', $file->getCTime()) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Ngày sửa</strong>: <span>' . date('d.m.Y - H:i:s', $file->getMTime()) . '</span></li>';

echo '</ul>';

file_display_actions($curr_path);

require SITE_FOOTER;
