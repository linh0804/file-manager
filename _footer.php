<?php

defined('ACCESS') or exit;

$curr_path = get_curr_path();

if (IS_LOGIN) {
    if (auth_get_login_fail()) {
        $site_sidebar .= '<div class="list" style="font-size: small; font-style: italic">
            fail login: <span style="color: red; font-weight: bold">' . auth_get_login_fail() . '</span>
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

echo '<script>nightmare_scrolltop.init();</script>';

echo '<div id="menu-overlay"></div>
    <div id="box-overlay"></div>';

echo '<script>edit_recent.render("fm_edit_recent_list");</script>';

echo '</div>

<script>
    setTimeout(() => {
        $.get("cron.php", function(html) {
            $("#app-index-updater").html(html);
        });
    }, 1000);
</script>

</body>
</html>';
