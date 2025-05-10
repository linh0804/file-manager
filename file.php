<?php

define('ACCESS', true);

require '.init.php';

if (!isLogin) {
    goURL('login.php');
}

$action = request()->get('act');
$path = (string) request()->get('path');
$path = rawurldecode($path);

check_path($path);

switch ($action) {
    case 'rename':
        $error = '';
        $name = request()->post('name', basename($path));
        $newPath = dirname($path) . '/' . $name;
        $title = 'Đổi tên tập tin';
        
        if (request()->has_post('submit')) {    
            $error .= '<div class="notice_failure">';

            if (empty($name)) {
                $error .= 'Chưa nhập đầy đủ thông tin';
            } elseif (isNameError($name)) {
                $error .= 'Tên tập tin không hợp lệ';
            } elseif (file_exists($newPath)) {
                $error .= 'Tên tập tin đã tồn tại';
            } elseif (!rename($path, $newPath)) {
                $error .= 'Thay đổi thất bại';
            } else {
                goURL('index.php?path=' . dirname($path) . $pages['paramater_1']);
            }

            $error .= '</div>';
        }

        require 'header.php';

        echo '<div class="title">' . $title . '</div>';       
        
        echo $error;

        echo '<div class="list">
          <span class="bull">&bull;</span><span>' . printPath($path) . '</span><hr/>
          <form action="" method="post">
            <span class="bull">&bull;</span>Tên tập tin:<br/>
            <input type="text" name="name" value="' . $name . '" /><br/>
            <input type="submit" name="submit" value="Thay đổi"/>
          </form>
        </div>';

        show_back();
    
        require 'footer.php';
        break;

    default:
        // folder info
        if (is_dir($path)) {
            $title = 'Thông tin thư mục';

            require 'header.php';
            
            echo '<div class="title">' . $title . '</div>';
            
            $dir = processDirectory($path);
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
                    
            require 'footer.php';            
            exit;
        }

        // file info
        $title = 'Thông tin tập tin';
        
        require 'header.php';

        echo '<div class="title">' . $title . '</div>';

        $dir = dirname($path);
        $name = basename($path);
        $file = new SplFileInfo($path);
        $format = $file->getExtension();
        $isImage = false;
        $pixel = null;

        echo '<ul class="info">';
        echo '<li class="not_ellipsis"><span class="bull">&bull; </span><strong>Đường dẫn</strong>: <span>' . printPath($dir, true) . '</span></li>';

        if ($format && in_array($format, array('png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'))) {
            $pixel = getimagesize($path);
            $isImage = true;

            echo '<li><center><img src="read_image.php?path=' . rawurlencode($path) . '" width="' . ($pixel[0] > 200 ? 200 : $pixel[0]) . 'px"/></center><br/></li>';
        }

        echo '<li><span class="bull">&bull; </span><strong>Tên</strong>: <span>' . $name . '</span></li>
            <li><span class="bull">&bull; </span><strong>Kích thước</strong>: <span>' . size($file->getSize()) . '</span></li>
            <li><span class="bull">&bull; </span><strong>Chmod</strong>: <span>' . getChmod($path) . '</span></li>';

        if ($isImage) {
            echo '<li><span class="bull">&bull; </span><strong>Độ phân giải</strong>: <span>' . $pixel[0] . 'x' . $pixel[1] . '</span></li>';
        }

        echo '<li><span class="bull">&bull; </span><strong>Định dạng</strong>: <span>' . ($format == null ? 'Không rõ' : $format) . '</span></li>
            <li><span class="bull">&bull; </span><strong>Ngày sửa</strong>: <span>' . @date('d.m.Y - H:i', filemtime($path)) . '</span></li>';
        echo '<li><span class="bull">&bull; </span><strong>Owner</strong>: <span>' . (posix_getpwuid($file->getOwner())['name']) . '</span></li>';
        echo '</ul>';

        printFileActions($file);
    
        require 'footer.php';
}
