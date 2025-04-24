<?php

const ACCESS = true;
const LOGIN  = true;

require '.init.php';

if (isLogin) {
    goURL('index.php');
}

$title = 'Đăng nhập';
$notice = null;

if (!ableLogin()) {
    require 'header.php';
    echo '<div class="title">' . $title . '</div>';
    echo '<div class="notice_failure">
        Khoá đăng nhập, vào thư mục manager xoá file "' . LOGIN_LOCK . '" để mở khoá!<br><br>
        Hoặc bạn có thể đăng nhập lại sau "30 phút" tính từ lần đăng nhập "cuối cùng"!
    </div>';
    require 'footer.php';
    exit;
}

if (isset($_POST['submit'])) {
    $notice   = '<div class="notice_failure">';
    $username = addslashes($_POST['username']);
    $password = addslashes($_POST['password']);

    if ($username == null || $password == null) {
        $notice .= 'Chưa nhập đầy đủ thông tin';
    } elseif (
        strtolower($username) != strtolower($configs['username'])
        || getPasswordEncode($password) != $configs['password']
    ) {
        $notice .= 'Sai tài khoản hoặc mật khẩu.';

        // khoá đăng nhập sau 5 lần
        increaseLoginFail();
        $notice .= ' Bạn còn ' . (LOGIN_MAX - getLoginFail()) . ' lần thử!';
    } else {
        removeLoginFail();
        setcookie(FM_COOKIE_NAME, getPasswordEncode($password), time() + 3600 * 24 * 365);

        goURL('index.php');
    }

    $notice .= '</div>';
}

require 'header.php';

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
    if (createConfig()) {
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

require 'footer.php';
