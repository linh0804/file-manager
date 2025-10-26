<?php
namespace app;

define('ACCESS', true);
define('LOGIN', true);

require '_init.php';

if (isLogin) {
    redirect('index.php');
}

$title = 'Đăng nhập';
$notice = null;

if (!able_login()) {
    require '_header.php';
    echo '<div class="title">' . $title . '</div>';
    echo '<div class="notice_failure">
        Khoá đăng nhập, vào thư mục manager sửa file .config.php, sửa "' . LOGIN_LOCK . '" = 0 để mở khoá! Hoặc xoá nó đi để reset!!! =)))<br><br>
        Hoặc bạn có thể đăng nhập lại sau "30 phút" tính từ lần đăng nhập "cuối cùng"!
    </div>';
    require '_footer.php';
    exit;
}

if (isset($_POST['submit'])) {
    $notice   = '<div class="notice_failure">';
    $username = addslashes((string) $_POST['username']);
    $password = addslashes((string) $_POST['password']);

    if ($username == null || $password == null) {
        $notice .= 'Chưa nhập đầy đủ thông tin';
    } elseif (
        strtolower($username) != strtolower((string) $configs['username'])
        || get_password_encode($password) != $configs['password']
    ) {
        $notice .= 'Sai tài khoản hoặc mật khẩu.';

        // khoá đăng nhập sau 5 lần
        increase_login_fail();
        $notice .= ' Bạn còn ' . (LOGIN_MAX - get_login_fail()) . ' lần thử!';
    } else {
        remove_login_fail();
        setcookie(FM_COOKIE_NAME, (string) get_password_encode($password), time() + 3600 * 24 * 365);

        redirect('index.php');
    }

    $notice .= '</div>';
}

require '_header.php';

echo '<div class="title">' . $title . '</div>';
echo $notice;

if (IS_CONFIG_UPDATE || IS_CONFIG_ERROR) {
    @unlink(PATH_CONFIG);
}

if (IS_CONFIG_UPDATE) {
    echo '<div class="notice_info">Cấu hình cập nhật sẽ đưa về mặc định</div>';
} elseif (IS_CONFIG_ERROR) {
    echo '<div class="notice_failure">Cấu hình bị lỗi sẽ đưa về mặc định</div>';
} elseif (!is_file(PATH_CONFIG)) {
    echo '<div class="notice_info">Cấu hình không tồn tại nó sẽ được tạo</div>';
}


if (!is_file(PATH_CONFIG)) {
    if (create_config()) {
        echo '<div class="notice_info">Tài khoản: <strong>' . LOGIN_USERNAME_DEFAULT . '</strong>, Mật khẩu: <strong>' . LOGIN_PASSWORD_DEFAULT . '</strong></div>';
    } else {
        echo '<div class="notice_failure">Tạo cấu hình thất bại, hãy thử lại</div>';
    }
}

echo '<div class="list">
    <form action="login.php" method="post">
        <span class="bull">&bull; </span>Tên đăng nhập:<br/>
        <input type="text" name="username" value="" size="18"/><br/>
        <span class="bull">&bull; </span>Mật khẩu:<br/>
        <input type="password" name="password" value="" size="18"/><br/>
        <input type="submit" name="submit" value="Đăng nhập"/>
    </form>
</div>';

require '_footer.php';
