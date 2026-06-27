<?php

define('ACCESS', true);
require __DIR__ . '/_init.php';

$site_title = 'Cài đặt';

require SITE_HEADER;

echo '<div class="title">' . $site_title . '</div>';

$username = config()->get('username');
$password_o = null;
$password_n = null;
$password_nr = null;

if (isset($_POST['submit'])) {
    $username = (string) $_POST['username'];
    $password_o = (string) $_POST['password_o'];
    $password_n = (string) $_POST['password_n'];
    $password_nr = (string) $_POST['password_nr'];

    if (empty($username)) {
        echo '<div class="notice_failure">Chưa nhập tên đăng nhập</div>';
    } elseif (strlen($username) < 3) {
        echo '<div class="notice_failure">Tên đăng nhập phải lớn hơn 3 ký tự</div>';
    } elseif (!empty($password_o) && auth_encode_pwd($password_o) != config()->get('password')) {
        echo '<div class="notice_failure">Mật khẩu cũ không đúng</div>';
    } elseif (!empty($password_o) && (empty($password_n) || empty($password_nr))) {
        echo '<div class="notice_failure">Để thay đổi mật khẩu hãy nhập đủ hai mật khẩu</div>';
    } elseif (!empty($password_o) && $password_n != $password_nr) {
        echo '<div class="notice_failure">Hai mật khẩu không giống nhau</div>';
    } elseif (!empty($password_o) && strlen($password_n) < 5) {
        echo '<div class="notice_failure">Mật khẩu phải lớn hơn 5 ký tự</div>';
    } else {
        config()->set([
            'username' => $username,
            'password' => !empty($password_n) ? auth_encode_pwd($password_n) : config()->get('password'),
        ]);

        $username = config()->get('username');
        $password_o = null;
        $password_n = null;
        $password_nr = null;

        echo '<div class="notice_succeed">Lưu thành công</div>';
    }
}

echo '<form action="' . action_link('setting') . '" method="post">
    <div class="list">
    <span class="bull">&bull; </span>Tài khoản:<br/>
    <input type="text" name="username" value="' . $username . '" size="18"/><br/>

    <span class="bull">&bull; </span>Mật khẩu cũ:<br/>
    <input type="password" name="password_o" value="' . $password_o . '" size="18"/><br/>

    <span class="bull">&bull; </span>Mật khẩu mới:<br/>
    <input type="password" name="password_n" value="' . $password_n . '" size="18"/><br/>

    <span class="bull">&bull; </span>Nhập lại mật khẩu mới:<br/>
    <input type="password" name="password_nr" value="' . $password_nr . '" size="18"/><br/>

    <input type="submit" name="submit" value="Lưu"/>
    </div>
</form>';

echo '<div class="tips"><img src="icon/tips.png" alt=""/> Mật khẩu để trống nếu không muốn thay đổi</div>
    <div class="title">Chức năng</div>';

echo '<ul class="list">
  <li><a href="' . action_link('reinstall') . '" class="button"><img src="icon/empty.png" alt=""/> Cài đặt lại!!!</a></li>
</ul>';

echo '<div class="list">Thư mục cài đặt: ' . htmlspecialchars(__DIR__) . '</div>';

require SITE_FOOTER;
