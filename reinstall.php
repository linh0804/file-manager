<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

$site_title = 'Cài đặt lại Manager!!!';
$error = '';

if (isset($_POST['submit'])) {
    if (app_reinstall()) {
        redirect(action_link('index'));
    }

    $error = '<div class="list">Lỗi</div>';
}

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

if ($error) {
    echo $error;
} else {
    echo '<div class="list">
        <span>Cài đặt lại Manager? Bạn phải tự chịu rủi ro khi thực hiện thao tác này!!!</span><hr />
        <form method="post">
            <input type="hidden" name="token" value="' . time() . '" />
            <input type="submit" name="submit" value="Xác nhận!!!"/>
        </form>
    </div>';
}

require SITE_FOOTER;
