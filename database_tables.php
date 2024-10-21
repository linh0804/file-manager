<?php

define('ACCESS', true);
define('PHPMYADMIN', true);

include_once 'function.php';

$title = 'Danh sách bảng';

include_once 'database_connect.php';

if (IS_CONNECT) {
    $title .= ': ' . DATABASE_NAME;
    $query = mysqli_query($MySQLi, 'SHOW TABLE STATUS');

    include_once 'header.php';

    if ($query !== false) {
        echo '<div class="title"><div class="ellipsis">' . $title . '</div></div>
                <ul class="list_database">';

        $count = mysqli_query($MySQLi, 'SELECT COUNT(*) as `c` FROM `information_schema`.`tables` WHERE `table_schema`="' . DATABASE_NAME . '"');
        $count = mysqli_fetch_object($count);
        $count = (int) $count->c;

        if ($count == 0) {
            echo '<li class="normal"><img src="icon/empty.png"/> <span class="empty">Không có bảng nào</span></li>';
        } else {
            $total_size = 0;

            while ($assoc = mysqli_fetch_assoc($query)) {
                $name = $assoc['Name'];
                $total_size += intval($assoc['Data_length']);

                echo '<li>
                            <p>
                                <a href="database_table.php?action=rename&name=' . $name . DATABASE_NAME_PARAMATER_1 . '">
                                    <img src="icon/database_table.png"/>
                                </a>
                                <a href="database_table.php?start&name=' . $name . DATABASE_NAME_PARAMATER_1 . '">
                                    <strong>' . $name . '</strong>
                                </a>
                            </p>
                            <p>
                                <span class="size">' . size($assoc['Data_length']) . '</span>, 
                                <span class="count_columns">' . ($assoc['Rows'] == 0 ? mysqli_query($MySQLi, "SHOW COLUMNS FROM `$name`")->num_rows : $assoc['Rows']) . '</span>
                                <span>cột</span>
                            </p>
                        </li>';
            }

            echo '<li class="normal"><strong>Dung lượng</strong>: <span class="size">' . size($total_size) . '</span>, <strong>Bảng</strong>: <span class="count_tables">' . $count . '</span></li>';
        }

        echo '</ul>
                <div class="title">Chức năng</div>
                <ul class="list">
                    <li><img src="icon/database.png"/> <a href="database_query.php">SQL</a></li>
                    <li><img src="icon/database_table_create.png"/> <a href="database_table_create.php' . DATABASE_NAME_PARAMATER_0 . '">Tạo bảng</a></li>';

        if (IS_DATABASE_ROOT) {
            echo '<li><img src="icon/database.png"/> <a href="database_lists.php">Danh sách database</a></li>';
        }

        echo '</ul>';
    } else {
        echo '<div class="title"><div class="ellipsis">' . $title . '</div></div>
                <div class="list">Không thể lấy danh sách bảng</div>
                <div class="title">Chức năng</div>
                <ul class="list">';

        if (IS_DATABASE_ROOT) {
            echo '<li><img src="icon/database.png"/> <a href="database_lists.php">Danh sách database</a></li>';
        } else {
            echo '<li><img src="icon/disconnect.png"/> <a href="database_disconnect.php">Ngắt kết nối database</a></li>';
        }

        echo '</ul>';
    }
} elseif (ERROR_CONNECT == false && ERROR_SELECT_DB && IS_DATABASE_ROOT) {
    include_once 'header.php';

    echo '<div class="title">' . $title . '</div>
            <div class="list">Không thể chọn database</div>
            <div class="title">Chức năng</div>
            <ul class="list">
                <li><img src="icon/database.png"/> <a href="database_lists.php">Danh sách database</a></li>
            </ul>';
} else {
    include_once 'header.php';

    echo '<div class="title">' . $title . '</div>
            <div class="list">Lỗi cấu hình hoặc không kết nối được</div>
            <div class="title">Chức năng</div>
            <ul class="list">
                <li><img src="icon/disconnect.png"/> <a href="database_disconnect.php">Ngắt kết nối database</a></li>
            </ul>';
}

include_once 'footer.php';
include_once 'database_close.php';
