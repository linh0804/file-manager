<?php

define('ACCESS', true);

require '.init.php';

$title = 'Kết nối database';

require 'header.php';

$host = 'localhost';
$username = 'root';
$password = '';
$name = '';
$notice = '';
$auto = true;
$go = false;

if (is_file(pathDatabase)) {
    $databases = require pathDatabase;

    if (isDatabaseVariable($databases)) {
        $host = $databases['db_host'];
        $username = $databases['db_username'];
        $password = $databases['db_password'];
        $name = $databases['db_name'];
        $auto = $databases['is_auto'];

        if ($auto && !isset($_POST['submit'])) {
            if (!$connectTemp = @mysqli_connect($host, $username, $password)) {
                $notice = '<div class="notice_failure">Không thể kết nối tới database</div>';
            } elseif (!empty($name) && !@mysqli_select_db($connectTemp, $name)) {
                $notice = '<div class="notice_failure">Không thể chọn database</div>';
            } else {
                $go = true;
            }
        }
    } elseif (!isset($_POST['submit'])) {
        @unlink(pathDatabase);

        $notice = '<div class="notice_failure">Cấu hình database bị lỗi</div>';
    }
}

if (isset($_POST['submit'])) {
    $host = $_POST['host'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $name = $_POST['name'] ?? '';
    $auto = isset($_POST['is_auto']);

    if (empty($host) || empty($username)) {
        $notice = '<div class="notice_failure">Chưa nhập đầy đủ thông tin</div>';
    } elseif (!$connectTemp = @mysqli_connect($host, $username, $password)) {
        $notice = '<div class="notice_failure">Không thể kết nối tới database</div>';
    } elseif (!empty($name) && !@mysqli_select_db($connectTemp, $name)) {
        $notice = '<div class="notice_failure">Không thể chọn database</div>';
    } else {
        if (createDatabaseConfig([
            'db_host' => $host,
            'db_username' => $username,
            'db_password' => $password,
            'db_name' => $name,
            'is_auto' => $auto
        ])) {
            $go = true;
        } else {
            $notice = '<div class="notice_failure">Lưu cấu hình database thất bại</div>';
        }
    }
}

if ($go) {
    if (empty($name)) {
        goURL('database_lists.php');
    } else {
        goURL('database_tables.php');
    }
}

echo '<div class="title">' . $title . '</div>';
echo $notice;
echo '<div class="list">
    <form method="post">
        <span class="bull">&bull;</span>Host:<br/>
        <input type="text" name="host" value="' . htmlspecialchars($host) . '" size="18"/><br/>
        <span class="bull">&bull;</span>Tài khoản database:<br/>
        <input type="text" name="username" value="' . htmlspecialchars($username) . '" size="18"/><br/>
        <span class="bull">&bull;</span>Mật khẩu database:<br/>
        <input type="password" name="password" value="' . htmlspecialchars($password) . '" size="18" autocomplete="off"/><br/>
        <span class="bull">&bull;</span>Tên database:<br/>
        <input type="text" name="name" value="' . htmlspecialchars($name) . '" size="18"/><br/>
        <label><input type="checkbox" name="is_auto" value="1"' . ($auto ? ' checked="checked"' : null) . '/>Tự động kết nối</label><br/>
        <input type="submit" name="submit" value="Kết nối"/>
    </form>
</div>

<div class="tips"><img src="icon/tips.png"/> Tên database để trống nếu bạn muốn kết nối vào danh sách database.</div>

<div class="title">Chức năng</div>
<ul class="list">
    <li><img src="icon/list.png"/> <a href="index.php">Quản lý tập tin</a></li>
</ul>';

require 'footer.php';
