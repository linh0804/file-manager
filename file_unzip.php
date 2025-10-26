<?php
namespace app;

use ngatngay\fs;
use ngatngay\http\request;
use ZipArchive;
use SplFileInfo;

define('ACCESS', true);

require '_init.php';

check_path($path, 'file');

$error = '';
$title = 'Giải nén tập tin';
$file = new SplFileInfo($path);
$format = $file->getExtension();
$path_unzip = request::post('path_unzip', dirname((string) $path));
$is_delete = request::has_post('is_delete');

require '_header.php';

echo '<div class="title">' . $title . '</div>';

if (!in_array($format, array('zip', 'jar'))) {
    echo '<div class="list"><span>Tập tin không phải zip</span></div>';
    show_back();
} else {
    if (request::is_method('post')) {
        $error .= '<div class="notice_failure">';

        if (empty($path_unzip)) {
            $error .= 'Chưa nhập đầy đủ thông tin';
        } elseif (!is_dir($path_unzip)) {
            $error .= 'Đường dẫn giải nén không tồn tại';
        } else {
            $zip = new ZipArchive();

            if ($zip->open($path) === true) {
                $zip->extractTo($path_unzip);
                $zip->close();

                if ($is_delete) {
                    fs::remove($path);
                }

                redirect('index.php?path=' . dirname((string) $path) . $pages['paramater_1']);
            } else {
                $error .= 'Giải nén tập tin lỗi';
            }
        }

        $error .= '</div>';
    }
    
    echo $error;

    echo '<div class="list">
        <span class="bull">&bull;</span><span>' . print_path($path) . '</span><hr/>
        <form method="post">
            <span class="bull">&bull;</span>Đường dẫn giải nén:<br/>
            <textarea name="path" data-autoresize>' . $path_unzip . '</textarea><br/>
            <input type="checkbox" name="is_delete" value="1"' . ($is_delete ? ' checked="checked"' : '') . '/> Xóa tập tin zip<br/>
            <input type="submit" name="submit" value="Giải nén"/>
        </form>
    </div>';
    
    print_actions($file);
}

require '_footer.php';
