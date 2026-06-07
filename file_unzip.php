<?php

use nightmare\fs;
use nightmare\http\request;

defined('ACCESS') or exit;

$error = '';
$site_title = 'Giải nén tập tin';
$file = new SplFileInfo($curr_path);
$format = $file->getExtension();
$path_unzip = request::post('path_unzip', dirname((string) $curr_path));
$is_delete = request::has_post('is_delete');

require SITE_HEADER;



echo '<div class="title">' . $site_title . '</div>';

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

            if ($zip->open($curr_path) === true) {
                $zip->extractTo($path_unzip);
                $zip->close();

                if ($is_delete) {
                    fs::remove($curr_path);
                }

                redirect(action_link('index', ['path' => dirname((string) $curr_path)] + get_page_list_params()));
            } else {
                $error .= 'Giải nén tập tin lỗi';
            }
        }

        $error .= '</div>';
    }
    
    echo $error;

    echo '<div class="list">
        <span class="bull">&bull;</span><span>' . print_path($curr_path) . '</span><hr/>
        <form method="post">
            <span class="bull">&bull;</span>Đường dẫn giải nén:<br/>
            <input type="text" name="path_unzip" value="' . $path_unzip . '"/><br/>
            <input type="checkbox" name="is_delete" value="1"' . ($is_delete ? ' checked="checked"' : '') . '/> Xóa tập tin zip<br/>
            <input type="submit" name="submit" value="Giải nén"/>
        </form>
    </div>';
    
    print_actions($file);
}
require SITE_FOOTER;
