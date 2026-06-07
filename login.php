<?php

define('ACCESS', true);
define('LOGIN_BYPASS_AUTO_REDIRECT', true);

require __DIR__ . '/_init.php';

if (IS_LOGIN) {
    redirect(action_link('index'));
}

$site_title = 'Đăng nhập';
$notice = null;

if (!can_login()) {
    require SITE_HEADER;
    echo '<div class="title">' . $site_title . '</div>';
    echo '<div class="notice_failure">
        Khoá đăng nhập, bạn có thể đăng nhập lại sau "' . LOGIN_WAIT . ' giây" tính từ bây giờ!
    </div>';
    require SITE_FOOTER;
    exit;
}

if (isset($_POST['submit'])) {
    $notice   = '<div class="notice_failure">';
    $username = (string) $_POST['username'];
    $password = (string) $_POST['password'];

    if (empty($username) || empty($password)) {
        $notice .= 'Chưa nhập đầy đủ thông tin';
    } elseif (
        strtolower($username) != strtolower((string) config()->get('username'))
        || auth_encode_pwd($password) != config()->get('password')
    ) {
        $notice .= 'Sai tài khoản hoặc mật khẩu.';

        // khoá đăng nhập sau 5 lần
        increase_login_fail();
        $notice .= ' Bạn còn ' . (LOGIN_MAX - get_login_fail()) . ' lần thử!';
    } else {
        reset_fail_login();
        setcookie(APP_NAME . '_auth', (string) auth_encode_pwd($password), time() + 3600 * 24 * 365);

        redirect(action_link('index'));
    }

    $notice .= '</div>';
}

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';
echo $notice;

if (IS_CONFIG_ERROR) {
    echo '<div class="notice_failure">Cấu hình bị lỗi sẽ đưa về mặc định</div>';

    config()->set('username', LOGIN_USERNAME_DEFAULT);
    config()->set('password', auth_encode_pwd(LOGIN_PASSWORD_DEFAULT));

    echo '<div class="notice_info">Tài khoản: <strong>' . LOGIN_USERNAME_DEFAULT . '</strong>, Mật khẩu: <strong>' . LOGIN_PASSWORD_DEFAULT . '</strong></div>';
}

echo '<div class="list">
    <form action="" method="post">
        <span class="bull">&bull; </span>Tên đăng nhập:<br/>
        <input type="text" name="username" value="" size="18"/><br/>
        <span class="bull">&bull; </span>Mật khẩu:<br/>
        <input type="password" name="password" value="" size="18"/><br/>
        <input type="submit" name="submit" value="Đăng nhập"/>
    </form>
</div>';

echo '<div class="tips">Cần trình duyệt bật Cookie để đăng nhập!</div>';

require SITE_FOOTER;
