<?php

namespace app;

use ngatngay\http\request;
use SplFileInfo;

define('ACCESS', true);

require '_init.php';

$path = get_path();
check_path($path);

$file = new SplFileInfo($path);
$title = 'Thông tin ' . t_file_type($path);

$format = $file->getExtension();
$isImage = false;
$pixel = null;

$dir_size = 0;
$total_file = 0;
$total_dir = 0;

require '_header.php';

echo '<div class="title">' . $title . '</div>';

echo '<ul class="info">';
echo '<li class="not_ellipsis"><span class="bull">&bull; </span><strong>Đường dẫn</strong>: <span>' . print_path($file, $file->isDir() ? true : false) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Tên</strong>: <span>' . basename((string) $file) . '</span></li>';

if ($file->isFile()) {
    if ($format && in_array($format, array('png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'))) {
        $pixel = getimagesize($path);
        $isImage = true;

        echo '<li><center><img src="read_image.php?path=' . rawurlencode($path) . '" width="' . ($pixel[0] > 200 ? 200 : $pixel[0]) . 'px"/></center><br/></li>';
    }

    echo '<li><span class="bull">&bull; </span><strong>Kích thước</strong>: <span>' . size($file->getSize()) . '</span></li>';

    if ($isImage) {
        echo '<li><span class="bull">&bull; </span><strong>Độ phân giải</strong>: <span>' . $pixel[0] . 'x' . $pixel[1] . '</span></li>';
    }
}

if ($file->isDir()) {
    echo '<li><span class="bull">&bull; </span><strong>Kích thước thư mục</strong>: <span>' . size(filesize($file)) . '</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Dung lượng thư mục</strong>: <span>' . size($dir_size) . ' (' . $dir_size . ' byte)</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Tổng số thư mục</strong>: <span>' . $total_dir . '</span></li>';    
    echo '<li><span class="bull">&bull; </span><strong>Tổng số file</strong>: <span>' . $total_file . '</span></li>';
}

echo '<li><span class="bull">&bull; </span><strong>Owner</strong>: <span>' . (posix_getpwuid($file->getOwner())['name']) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Chmod</strong>: <span>' . get_chmod($file) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Ngày sửa</strong>: <span>' . @date('d.m.Y - H:i', filemtime($file)) . '</span></li>';
    
echo '</ul>';

print_actions($path);

require '_footer.php';
