<?php

defined('ACCESS') or exit;

$curr_path = get_curr_path();

if (IS_LOGIN) {
    if (get_login_fail()) {
        $site_sidebar .= '<div class="list" style="font-size: small; font-style: italic">
            fail login: <span style="color: red; font-weight: bold">' . get_login_fail() . '</span>
        </div>';
    }

    // function
    $site_sidebar .= '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/home.png"/> <a href="' . action_link('setting_home') . '">Sửa Trang chủ</a></li>
        <li><img src="icon/search.png"/> <a href="' . action_link('folder_compare_simple') . '">So sánh thư mục</a></li>
        <li><img src="icon/mime/unknown.png"/> <a href="' . action_link('command', ['path' => $curr_path]) . '">Chạy lệnh</a></li>
        <li><img src="icon/mime/unknown.png"/> <a href="' . action_link('composer', ['path' => $curr_path]) . '">Chạy lệnh Composer</a></li>
        <li><img src="icon/mime/unknown.png"/> <a href="' . action_link('fix_permission', ['path' => $curr_path]) . '">Fix chown/chmod</a></li>
        <li><img src="icon/mime/php.png"/> <a href="' . action_link('phpinfo') . '">phpinfo()</a></li>
    </ul>';
    
    // bookmark
    $bookmarks = array_reverse(fm_bookmark::get());
    $site_sidebar .= '<style>
    ul.list li {
        white-space: normal;
        font-size: 12px;
    }
    </style>
    <div class="title">Bookmark</div>
    <ul class="list">';

    if (!empty($curr_path) && @is_dir($curr_path)) {
        $site_sidebar .= '<li>
        <img src="icon/create.png" />
        <a href="' . action_link('index', ['add_bookmark' => $curr_path]) . '">
            Thêm thư mục hiện tại
        </a>
        </li>';
    }

    foreach ($bookmarks as $bookmark) {
        $site_sidebar .= '<li>

        <a href="' . action_link('index', ['path' => $bookmark]) . '">
            ' . htmlspecialchars(rtrim(dirname((string) $bookmark), '/')) . '/<b>' . htmlspecialchars(basename((string) $bookmark)) . '</b>
        </a>
        <a href="' . action_link('index', ['delete_bookmark' => $bookmark]) . '">
            <span style="color: red">[X]</span>
        </a>
        </li>';
    }
    $site_sidebar .= '</ul>';

    // filelist
    $site_sidebar .= '<div class="title">Sửa gần đây</div>';
    $site_sidebar .= '<div class="list" id="fm_edit_recent_list"></div>';
    // end filelist

    $site_sidebar .= '<div class="list" style="font-size: small; font-style: italic">
        run on: ' . get_current_user() . ' (' . getmyuid() . ')
    </div>';

    echo '<div class="menu-toggle">' . $site_sidebar . '</div>';
}

echo '</div>';

echo '<div id="app-footer">
    <span>Version: <a href="https://github.com/linh0804/file-manager">' . APP_VERSION . '</a></span>
    <br><br>[ <a href="' . action_link('logout') . '">Đăng Xuất</a> ]
</div>';

echo '<div
    id="scroll"
    class="scroll-to-top scroll-to-top-icon"
    style="display: block; visibility: visible; opacity: 0.5; display: none;"
></div>';

echo '<div id="menu-overlay"></div>
    <div id="box-overlay"></div>';

echo '<script>app_edit_recent.render("fm_edit_recent_list");</script>';

echo '</div>
</body>
</html>';
