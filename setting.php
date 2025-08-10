<?php
namespace app;

define('ACCESS', true);
define('alwaysCheckUpdate', true);

require '.init.php';

$title = 'Cài đặt';
$ref   = $_POST['ref'] ?? (isset($_SERVER['HTTP_REFFRER']) ? $_SERVER['HTTP_REFERER'] : null);
$ref   = $ref != $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ? $ref : null;

require_once 'header.php';

echo '<div class="title">' . $title . '</div>';

$username = $configs['username'];
$passwordO = null;
$passwordN = null;
$verifyN = null;
$pageList = $configs['page_list'];
$pageFileEdit = $configs['page_file_edit'];
$pageFileEditLine = $configs['page_file_edit_line'];
$pageDatabaseListRows = $configs['page_database_list_rows'];

if (isset($_POST['submit'])) {
    $username = addslashes((string) $_POST['username']);
    $passwordO = addslashes((string) $_POST['password_o']);
    $passwordN = addslashes((string) $_POST['password_n']);
    $verifyN = addslashes((string) $_POST['verify_n']);
    $pageList = intval(addslashes((string) $_POST['page_list']));
    $pageFileEdit = intval(addslashes((string) $_POST['page_file_edit']));
    $pageFileEditLine = intval(addslashes((string) $_POST['page_file_edit_line']));
    $pageDatabaseListRows = addslashes((string) $_POST['page_database_list_rows']);

    if (empty($username)) {
        echo '<div class="notice_failure">Chưa nhập tên đăng nhập</div>';
    } elseif (strlen($username) < 3) {
        echo '<div class="notice_failure">Tên đăng nhập phải lớn hơn 3 ký tự</div>';
    } elseif (!empty($passwordO) && getPasswordEncode($passwordO) != $configs['password']) {
        echo '<div class="notice_failure">Mật khẩu cũ không đúng</div>';
    } elseif (!empty($passwordO) && (empty($passwordN) || empty($verifyN))) {
        echo '<div class="notice_failure">Để thay đổi mật khẩu hãy nhập đủ hai mật khẩu</div>';
    } elseif (!empty($passwordO) && $passwordN != $verifyN) {
        echo '<div class="notice_failure">Hai mật khẩu không giống nhau</div>';
    } elseif (!empty($passwordO) && strlen($passwordN) < 5) {
        echo '<div class="notice_failure">Mật khẩu phải lớn hơn 5 ký tự</div>';
    } elseif (
        $pageList <= 0
        || $pageFileEdit <= 0
        || $pageFileEditLine <= 0
        || $pageDatabaseListRows <= 0
    ) {
        echo '<div class="notice_failure">Phân trang phải lớn hơn 0</div>';
    } else {
        if (createConfig(
            $username,
            (!empty($passwordN) ? getPasswordEncode($passwordN) : $configs['password']),
            $pageList,
            $pageFileEdit,
            $pageFileEditLine,
            $pageDatabaseListRows,
            false
        )) {
            include PATH_CONFIG;

            $username = $configs['username'];
            $passwordO = null;
            $passwordN = null;
            $verifyN = null;
            $pageList = $configs['page_list'];
            $pageFileEdit = $configs['page_file_edit'];
            $pageFileEditLine = $configs['page_file_edit_line'];
            $pageDatabaseListRows = addslashes((string) $_POST['page_database_list_rows']);

            echo '<div class="notice_succeed">Lưu thành công</div>';
        } else {
            echo '<div class="notice_failure">Lưu thất bại</div>';
        }
    }
}

echo '<div class="list">
    <form action="setting.php" method="post">
    <span class="bull">&bull; </span>Tài khoản:<br/>
    <input type="text" name="username" value="' . $username . '" size="18"/><br/>

    <span class="bull">&bull; </span>Mật khẩu cũ:<br/>
    <input type="password" name="password_o" value="' . $passwordO . '" size="18"/><br/>

    <span class="bull">&bull; </span>Mật khẩu mới:<br/>
    <input type="password" name="password_n" value="' . $passwordN . '" size="18"/><br/>

    <span class="bull">&bull; </span>Nhập lại mật khẩu mới:<br/>
    <input type="password" name="verify_n" value="' . $verifyN . '" size="18"/><br/>

    <span class="bull">&bull; </span>Phân trang danh sách:<br/>
    <input type="text" name="page_list" value="' . $pageList . '" size="18"/><br/>

    <span class="bull">&bull; </span>Phân trang sửa văn bản thường:<br/>
    <input type="text" name="page_file_edit" value="' . $pageFileEdit . '" size="18"/><br/>

    <span class="bull">&bull; </span>Phân trang sửa văn bản theo dòng:<br/>
    <input type="text" name="page_file_edit_line" value="' . $pageFileEditLine . '" size="18"/><br/>

    <span class="bull">&bull;</span>Phân trang danh sách dữ liệu sql:<br/>
    <input type="text" name="page_database_list_rows" value="' . $pageDatabaseListRows . '" size="18"/><br/>

    <input type="hidden" name="ref" value="' . $ref . '"/>

    <input type="submit" name="submit" value="Lưu"/>
    </form>
    </div>
    <div class="tips"><img src="icon/tips.png" alt=""/> Mật khẩu để trống nếu không muốn thay đổi</div>
    <div class="title">Chức năng</div>
    <ul class="list">';

if ($ref != null) {
    echo '<li><img src="icon/back.png" alt=""/> <a href="' . $ref . '">Quay lại</a></li>';
}

echo '<li>
  <a href="update.php" class="button"><img src="icon/download.png" alt=""/> Cập nhật</a>
  <a href="reinstall.php" class="button"><img src="icon/empty.png" alt=""/> Cài đặt lại!!!</a>
</li>';
echo '</ul>';

echo '<div class="list">Thư mục cài đặt: ' . htmlspecialchars(rootPath) . '</div>';

echo '<a href="javascript:history.back()" style="">
    <img src="icon/back.png"> 
    <strong class="back">Trở lại</strong>
</a>';

require 'footer.php';
