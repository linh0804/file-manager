<?php

define('ACCESS', true);
require __DIR__ . '/file.php';

if (isset($_FILES['file'])) {
    $name = (string) ($_FILES['file']['name'] ?? '');

    if (empty($name)) {
        response_api(['msg' => 'Tập tin bị lỗi!']);
    } else if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
        response_api(['msg' => 'Tập tin ' . $name . ' vượt quá kích thước cho phép']);
    } else {
        $new_path = $curr_path . '/' . $name;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $new_path)) {
            response_api(['msg' => 'Tập tin bị lỗi!']);
        }

        response_api([
            'status' => true,
            'msg' => 'Tải lên tập tin ' . $name . ' thành công',
            'reload_after_close' => true
        ]);
    }
}

$site_title = 'Tải lên tập tin';

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo '<form id="file-upload" action="' . get_curr_url_esc() . '" method="post" enctype="multipart/form-data">';

echo '<div class="list">';
echo '<span>' . file_print_path($curr_path, true) . '</span>';
echo '</div>';
 
echo '<div id="file-list"></div>';
echo '<input id="files" type="file" multiple style="display:none">';

echo '<div class="list">
    <button type="button" id="button-choose" class="button"><img src="icon/file.png" alt=""/> Chọn file</button>
    <button type="button" id="button-reset" class="button"><img src="icon/delete.png" alt=""/> Reset</button>
    <br>
    <button type="button" id="button-upload" class="button"><img src="icon/upload.png" alt=""/> Tải lên</button>
</div>';

echo '</form>';

require SITE_FOOTER;
