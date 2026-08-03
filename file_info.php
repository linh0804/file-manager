<?php

use Nightmare\Fs;

defined('ACCESS') or exit;

$file_name = basename($curr_path);
$site_title = 'Thông tin: ' . basename($curr_path);

require SITE_HEADER;

echo '<div class="title">' . file_print_path($curr_path, true) . '</div>';

echo '<ul class="info">';

echo '<li><span class="bull">&bull; </span><strong>Tên</strong>: <span style="color: red">' . $file_name . '</span></li>';

if (is_file($curr_path)) {
    $format = file_get_ext(basename($curr_path));
    $is_image = false;
    $pixel = null;

    if ($format && in_array($format, array('png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'))) {
        $pixel = getimagesize($curr_path);
        $is_image = true;

        echo '<li><center><img src="' . act_link('file', ['act' => 'download_image', 'path' => $curr_path]) . '" width="' . ($pixel[0] > 200 ? 200 : $pixel[0]) . 'px"/></center><br/></li>';
    }

    echo '<li><span class="bull">&bull; </span><strong>Kích thước</strong>: <span>' . Fs::sizen(filesize($curr_path)) . '</span></li>';

    if ($is_image) {
        echo '<li><span class="bull">&bull; </span><strong>Độ phân giải</strong>: <span>' . $pixel[0] . 'x' . $pixel[1] . '</span></li>';
    }
}

echo '<li><span class="bull">&bull; </span><strong>Owner</strong>: <span>' . (posix_getpwuid(fileowner($curr_path))['name']) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Chmod</strong>: <span>' . file_get_chmod($curr_path) . '</span></li>';

echo '<li><span class="bull">&bull; </span><strong>Ngày tạo</strong>: <span>' . date('d.m.Y - H:i:s', filectime($curr_path)) . '</span></li>';
echo '<li><span class="bull">&bull; </span><strong>Ngày sửa</strong>: <span>' . date('d.m.Y - H:i:s', filemtime($curr_path)) . '</span></li>';

echo '</ul>';

file_display_actions($curr_path);

require SITE_FOOTER;
