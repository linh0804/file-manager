<?php

define('ACCESS', true);
define('PHPMYADMIN', true);

require '.init.php';
require 'database_connect.php';

if (!IS_CONNECT) {
    goURL('database.php');
}

$title = 'Truy vấn CSDL';
$query = isset($_POST['query']) ? trim($_POST['query']) : '';
$affRows = -1;

require 'header.php';

echo '<div class="title"><div class="ellipsis">' . $title . '</div></div>';
if (DATABASE_NAME) {
    echo '<div class="list">Database: <b>' . DATABASE_NAME . '</b></div>';
}

if (isset($_POST['exec']) && !empty($query)) {
    @mysqli_multi_query($MySQLi, $query);
    do {
        if (mysqli_errno($MySQLi)) {
            break;
        }

        if ($result = mysqli_store_result($MySQLi)) {
            //
        }

        $affRows = mysqli_affected_rows($MySQLi);
    } while (@mysqli_next_result($MySQLi));

    if (mysqli_errno($MySQLi)) {
        echo '<div class="notice_failure">Truy vấn thất bại!<hr>';
        foreach(mysqli_error_list($MySQLi) as $e) {
            echo "<span style=\"color:black\">&bull;</span> ({$e['sqlstate']}/{$e['errno']}): {$e['error']}<br>";
        }
        echo '</div>';
    }

    echo '<div class="tips">Số hàng tác động: ' . $affRows . '</div>';
}

echo '<div class="list">
  <form method="post">
    <span class="bull">&bull; </span>Truy vấn:<br/>
    <textarea name="query" rows="10">' . htmlspecialchars($query) . '</textarea><br/>
    <input type="submit" name="exec" value="Thực hiện" />
    <input type="submit" name="demo" value="Chạy thử" />
  </form>
</div>';

echo '<div class="title">Chức năng</div>
<ul class="list">
  <li><img src="icon/database.png"/> <a href="database.php">Database</a></li>
</ul>';

require 'footer.php';
require 'database_close.php';
