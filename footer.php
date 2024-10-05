<?php

if (!defined('ACCESS')) die('Not access');

if (IS_LOGIN) {
    $menuToggle .= '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/search.png"/> <a href="folder_compare_simple.php">So sánh thư mục</a></li>
        <li><img src="icon/mime/unknown.png"/> <a href="run_command.php?dir=' . $dirEncode . '">Chạy lệnh</a></li>
        <li><img src="icon/mime/unknown.png"/> <a href="run_composer.php?dir=' . $dirEncode . '">Chạy lệnh Composer</a></li>
        <li><img src="icon/mime/unknown.png"/> <a href="fix_permission.php?dir=' . $dirEncode . '">Fix chown/chmod</a></li>
        <li><img src="icon/home.png"/> <a href="setting_home.php">Sửa Trang chủ</a></li>
        <li><img src="icon/mime/php.png"/> <a href="phpinfo.php">phpinfo()</a></li>
        <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . '">Danh sách</a></li>
    </ul>';


    // bookmark
    require __DIR__ . '/lib/bookmark.class.php';

    define('BOOKMARK_FILE', __DIR__ . '/bookmark.json');

    $Bookmark = new Bookmark(BOOKMARK_FILE);

    $add_bookmark = isset($_GET['add_bookmark']) ? trim($_GET['add_bookmark']) : '';
    if (!empty($add_bookmark)) {
        $add_bookmark = rawurldecode($add_bookmark);

        if (is_dir($add_bookmark)) {
            $Bookmark->add($add_bookmark);
            goURL('index.php?dir=' . rawurlencode($add_bookmark));
        }
    }

    $delete_bookmark = isset($_GET['delete_bookmark']) ? trim($_GET['delete_bookmark']) : '';
    if (!empty($delete_bookmark)) {
        $Bookmark->delete(rawurldecode($delete_bookmark));
        goURL('index.php');
    }

    $bookmarks = array_reverse($Bookmark->get());

    $menuToggle .= '<style>
    ul.list li {
        white-space: normal;
        font-size: small;
    }
    </style>
    <div class="title">Bookmark</div>
    <ul class="list">';

    if (
        !empty($dir)
        && is_dir(processDirectory($dir))
    ) {
        $menuToggle .= '<li>
        <img src="icon/create.png" />
        <a href="index.php?add_bookmark=' . rawurlencode($dir) . '">
            Thêm thư mục hiện tại
        </a>
        </li>';
    }

    foreach ($bookmarks as $bookmark) {
        $menuToggle .= '<li>

        <a href="index.php?dir=' . rawurlencode($bookmark) . '">
            ' . htmlspecialchars(dirname($bookmark)) . '/<b>' . htmlspecialchars(basename($bookmark)) . '</b>
        </a>
        <a href="index.php?delete_bookmark=' . rawurlencode($bookmark) . '">
            <span style="color: red">[X]</span>
        </a>
        </li>';
    }

    $menuToggle .= '</ul>';

    $menuToggle .= '<div class="list" style="font-size: small; font-style: italic">
        run on: ' . get_current_user() . ' (' . getmyuid() . ')
    </div>';

    if (file_exists(LOGIN_LOCK)) {
        $menuToggle .= '<div class="list" style="font-size: small; font-style: italic">
            fail login: <span style="color: red; font-weight: bold">' . getLoginFail() . '</span> (xoá <b>"' . htmlspecialchars(LOGIN_LOCK) . '"</b> để reset)
        </div>';
    }

    echo '<div class="menuToggle">
        ' . $menuToggle . '
    </div>';
}

echo '</div>';

echo '<div id="footer">
    <span>
		ngatngay cooperation with linh
	</span><br />
    <span>Version: ' . VERSION_MAJOR . '.' . VERSION_MINOR . '.' . VERSION_PATCH . '</span>
</div>';

echo '<div
    id="scroll"
    class="scroll-to-top scroll-to-top-icon"
    style="display: block; visibility: visible; opacity: 0.5; display: none;"
></div>';

echo '<div id="menuOverlay"></div><div id="boxOverlay"></div>';

echo '</body>
</html>';

ob_end_flush();

