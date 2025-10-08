<?php
namespace app;

defined('ACCESS') or exit('Not access');

if (isLogin) {
    if (getLoginFail()) {
        $menuToggle .= '<div class="list" style="font-size: small; font-style: italic">
            fail login: <span style="color: red; font-weight: bold">' . getLoginFail() . '</span>
        </div>';
    }

    // function
    $menuToggle .= '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/search.png"/> <a href="folder_compare_simple.php">So sánh thư mục</a></li>
        <li><img src="icon/mime/unknown.png"/> <a href="run_command.php?dir=' . $dirEncode . '">Chạy lệnh</a></li>
        <li><img src="icon/mime/unknown.png"/> <a href="run_composer.php?dir=' . $dirEncode . '">Chạy lệnh Composer</a></li>
        <li><img src="icon/mime/unknown.png"/> <a href="fix_permission.php?dir=' . $dirEncode . '">Fix chown/chmod</a></li>
        <li><img src="icon/home.png"/> <a href="setting_home.php">Sửa Trang chủ</a></li>
        <li><img src="icon/mime/php.png"/> <a href="phpinfo.php">phpinfo()</a></li>
    </ul>';
    
    // bookmark
    $bookmarks = array_reverse(bookmark_get());
    $menuToggle .= '<style>
    ul.list li {
        white-space: normal;
        font-size: small;
    }
    </style>
    <div class="title">Bookmark</div>
    <ul class="list">';

    if (!empty($path) && @is_dir($path)) {
        $menuToggle .= '<li>
        <img src="icon/create.png" />
        <a href="index.php?add_bookmark=' . $path . '">
            Thêm thư mục hiện tại
        </a>
        </li>';
    }

    foreach ($bookmarks as $bookmark) {
        $menuToggle .= '<li>

        <a href="index.php?path=' . rawurlencode((string) $bookmark) . '">
            ' . htmlspecialchars(rtrim(dirname((string) $bookmark), '/')) . '/<b>' . htmlspecialchars(basename((string) $bookmark)) . '</b>
        </a>
        <a href="index.php?delete_bookmark=' . $bookmark . '">
            <span style="color: red">[X]</span>
        </a>
        </li>';
    }
    $menuToggle .= '</ul>';

    // filelist
    $menuToggle .= '<div class="title">Sửa gần đây</div>';
    $menuToggle .= '<ul class="list">';
    
    foreach (config()->get('edit_recent', []) as $i) {
        $menuToggle .= '<li>
            <a href="edit_text.php?path=' . base64_encode((string) $i) . '">
            ' . htmlspecialchars(rtrim(dirname((string) $i), '/')) . '/<b>' . htmlspecialchars(basename((string) $i)) . '</b>
            </a>
        </li>';
    }
    
    $menuToggle .= '</ul>';
    // end filelist

    $menuToggle .= '<div class="list" style="font-size: small; font-style: italic">
        run on: ' . get_current_user() . ' (' . getmyuid() . ')
    </div>';

    echo '<div class="menuToggle">
        ' . $menuToggle . '
    </div>';
}

echo '</div>';

echo '<div id="footer">
    <span>Version: ' . localVersion . '</span>
    <br><a href="https://github.com/linh0804/file-manager">Github</a>
</div>';

echo '<div
    id="scroll"
    class="scroll-to-top scroll-to-top-icon"
    style="display: block; visibility: visible; opacity: 0.5; display: none;"
></div>';

echo '<div id="menuOverlay"></div><div id="boxOverlay"></div>';

echo '</body>
</html>';
