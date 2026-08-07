<?php

use Nightmare\Fs;

define('ACCESS', true);
require __DIR__ . '/file.php';

$site_title = 'Tải lên tập tin';

if (isset($_POST['submit'])) {
    $url = $_POST['url'] ?? '';

    if (!is_string($url) || trim($url) === '') {
        response_api(['msg' => 'Chưa nhập url nào cả']);
    }

    $url = trim($url);

    if (!is_url($url)) {
        response_api(['msg' => 'URL không hợp lệ']);
    }

    $name = basename($url);
    $path = $curr_path . '/' . $name;

    if (!file_import($path, $url)) {
        response_api(['msg' => 'Nhập khẩu tập tin ' . $name . ' thất bại']);
    }

    response_api([
        'status' => true,
        'msg' => $name . ', ' . Fs::sizen(filesize($path)),
        'reload_after_close' => true,
        'form_reset' => true
    ]);
}

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

echo '<div class="list">
    <span>' . file_print_path($curr_path, true) . '</span><hr/>

    <form data-ajax action="' . get_curr_url_esc() . '" method="post">
        <span class="bull">&bull; </span>URL:<br/>
        <input type="text" name="url"><br>

	<input type="submit" name="submit" value="Nhập khẩu"/>
    </form>
</div>';

require SITE_FOOTER;
