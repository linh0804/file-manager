<?php

define('ACCESS', true);

include_once '.init.php';

if (!isLogin) {
    goURL('login.php');
}

$title = 'Thông tin thư mục';

require_once 'header.php';

echo '<div class="title">' . $title . '</div>';

if (
    empty($dir)
    || !is_dir(processDirectory($dir))
) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
    </ul>';
} else {
    $dir = processDirectory($dir);
    $dirInfo = new SplFileInfo($dir);
    $files = readFullDir($dir);

    $dir_size = 0;
    $total_file = 0;
    $total_dir = 0;

    foreach ($files as $file) {
        if ($file->isFile()) {
            $total_file += 1;
            $dir_size += $file->getSize();
        }
        
        if ($file->isDir()) {
            $total_dir += 1;
        }
    }

    echo '<ul class="info">';
    
    echo '<li class="not_ellipsis"><span class="bull">&bull; </span><strong>Đường dẫn</strong>: <span>' . printPath($dir, true) . '</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Tên</strong>: <span>' . basename($dir) . '</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Kích thước thư mục</strong>: <span>' . size(filesize($dir)) . '</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Dung lượng thư mục</strong>: <span>' . size($dir_size) . ' (' . $dir_size . ' byte)</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Chmod</strong>: <span>' . getChmod($dir) . '</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Ngày sửa</strong>: <span>' . @date('d.m.Y - H:i', filemtime($dir)) . '</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Tổng số thư mục</strong>: <span>' . $total_dir . '</span></li>';    
    echo '<li><span class="bull">&bull; </span><strong>Tổng số file</strong>: <span>' . $total_file . '</span></li>';
    echo '<li><span class="bull">&bull; </span><strong>Owner</strong>: <span>' . (posix_getpwuid($dirInfo->getOwner())['name']) . '</span></li>';
    echo '</ul>';

    echo '<a href="javascript:history.back()" style="">
      <img src="icon/back.png"> 
      <strong class="back">Trở lại</strong>
    </a>';
}

require_once 'footer.php';
