<?php

use Nightmare\Http\Response;

define('ACCESS', true);
require __DIR__ . '/file.php';

if (isset($_FILES['file'])) {
    $data = [];
    $data['error'] = 'Tập tin bị lỗi!';

    if (!empty($_FILES['file']['name'])) {
        if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE) {
            $data['error'] = 'Tập tin ' . $_FILES['file']['name'] . ' vượt quá kích thước cho phép';
        } else {
            $newName = $curr_path . '/' . $_FILES['file']['name'];

            if (move_uploaded_file($_FILES['file']['tmp_name'], $newName)) {
                $data['error'] = '';
            }
        }
    }   
    
    response($data);
}

$site_title = 'Tải lên tập tin';

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo '<form id="file-upload" action="' . get_curr_url_esc() . '" enctype="multipart/form-data">';

echo '<div class="list">';
echo '<span>' . file_print_path($curr_path, true) . '</span>';
echo '</div>';
 
echo '<div id="file-list"></div>';
echo '<input id="files" type="file" multiple style="display:none">';

echo '<div class="list">
    <button id="button-choose" class="button"><img src="icon/file.png" alt=""/> Chọn file</button>
    <button id="button-reset" class="button"><img src="icon/delete.png" alt=""/> Reset</button>
    <br>
    <button id="button-upload" class="button"><img src="icon/upload.png" alt=""/> Tải lên</button>
</div>';

echo '</form>';

?>


<?php require SITE_FOOTER;
