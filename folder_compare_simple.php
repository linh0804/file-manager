<?php

define('ACCESS', true);

require_once '.init.php';

function is_duplicate_file($file1, $file2)
{
    if (filesize($file1) !== filesize($file2)) {
        return false;
    }

    if (sha1_file($file1) === sha1_file($file2)) {
        return true;
    }

    return false;
}

$title = 'So sánh thư mục';

require_once 'header.php';

echo '<div class="title">' . $title . '</div>';

$dir = processDirectory($dir);
$folder1 = isset($_POST['folder1']) ? rtrim(processDirectory($_POST['folder1']), '/') : '';
$folder2 = isset($_POST['folder2']) ? rtrim(processDirectory($_POST['folder2']), '/') : '';
$exclude = isset($_POST['exclude']) ? $_POST['exclude'] : '.git/' . PHP_EOL . 'node_modules/' . PHP_EOL . 'vendor/';

echo '<div class="list">
    <form method="post" autocomplete="off">
        Thư mục 1:<br />
        <input type="text" name="folder1" value="' . htmlspecialchars($folder1) . '" style="width: 80%" /><br />

        Thư mục 2:<br />
        <input type="text" name="folder2" value="' . htmlspecialchars($folder2) . '" style="width: 80%" /><br />

        Loại trừ theo biểu thức:<br />
        <textarea name="exclude" rows="5" style="width: 60%">' . htmlspecialchars($exclude) . '</textarea><br />
        <p style="font-size: small">
            Thư mục thì thêm / vào sau tên: <b>vendor/</b><br />
            Chỉ hỗ trợ loại trừ 1 cấp! Như: "vendor/" gồm("*/vendor/"). Không hỗ trợ "abc/vendor/".
        </p>

        <input type="submit" name="submit" value="So sánh" />
    </form>
</div>';

if (isset($_POST['submit'])) {
    $excludes = explode(PHP_EOL, $exclude);

    $files1 = [];
    $files1_only = [];

    $files2 = [];
    $files2_only = [];

    $files_intersect = [];
    $files_intersect_final = [];

    // kiểm tra
    if (!is_dir($folder1)) {
        echo '<div class="notice_failure">Thư mục 1 không hợp lệ</div>';
        goto display;
    }

    if (!is_dir($folder2)) {
        echo '<div class="notice_failure">Thư mục 2 không hợp lệ</div>';
        goto display;
    }

    // lay het file thu muc 1
    foreach(readFullDir($folder1, $excludes) as $file) {
        $files1[] = str_replace_first($folder1, '', $file->getPathname());
    }

    // lay het file thu muc 2
    foreach(readFullDir($folder2, $excludes) as $file) {
        $files2[] = str_replace_first($folder2, '', $file->getPathname());
    }

    $files1_only = array_diff($files1, $files2);
    $files2_only = array_diff($files2, $files1);
    $files_intersect = array_intersect($files1, $files2);

    // lay cac file khac nhau
    foreach ($files_intersect as $file) {
        $full_path_1 = $folder1 . $file;
        $full_path_2 = $folder2 . $file;

        if (!is_file($full_path_1) || !is_file($full_path_2)) {
            continue;
        }

        if (!is_duplicate_file(
            $full_path_1,
            $full_path_2
        )) {
            $files_intersect_final[] = $file;
        }
    }

    // Hiển thị
    display:


    echo '<style>
        #folder_compare a:link,
        #folder_compare a:visited {
            color: blue !important;
        }
    </style>';

    echo '<div id="folder_compare">';

    echo '<div class="list">
        <a href="#only1">Em</a>
        &bull;
        <a href="#only2">Anh</a>
        &bull;
        <a href="#diff">Chúng ta</a>
    </div>';

    // only 1
    echo '<div id="only1" class="title">Em (' . count($files1_only) . ')</div>';
    echo '<ul class="list">';
    foreach ($files1_only as $file) {
        $full_path = $folder1 . $file;
        $dir_url = rawurlencode(dirname($full_path));
        $name = basename($full_path);

        echo '<li>
            <span class="bull">&bull;</span> '
            . ltrim($file, '/')
            . ' [<a href="file.php?dir=' . $dir_url . '&name=' . $name . '">1</a>]
        </li>';
    }
    if (empty($files1_only)) {
        echo '<li>Trống</li>';
    }
    echo '</ul>';

    // only 2
    echo '<div id="only2" class="title">Anh (' . count($files2_only) . ')</div>';
    echo '<ul class="list">';
    foreach ($files2_only as $file) {
        $full_path = $folder2 . $file;
        $dir_url = rawurlencode(dirname($full_path));
        $name = basename($full_path);

        echo '<li>
            <span class="bull">&bull;</span> '
            . ltrim($file, '/')
            . ' [<a href="file.php?dir=' . $dir_url . '&name=' . $name . '">2</a>]
        </li>';
    }
    if (empty($files2_only)) {
        echo '<li>Trống</li>';
    }
    echo '</ul>';

    // diff
    echo '<div id="diff" class="title">Chung đường nhưng khác lối (' . count($files_intersect_final) . ')</div>';
    echo '<ul class="list">';
    foreach ($files_intersect_final as $file) {
        $full_path1 = $folder1 . $file;
        $dir_url1 = rawurlencode(dirname($full_path1));
        $name1 = basename($full_path1);

        $full_path2 = $folder2 . $file;
        $dir_url2 = rawurlencode(dirname($full_path2));
        $name2 = basename($full_path2);

        echo '<li>
            <span class="bull">&bull;</span> '
            . ltrim($file, '/')
            . ' [<a href="file.php?dir=' . $dir_url1 . '&name=' . $name1 . '">1</a>]'
            . ' [<a href="file.php?dir=' . $dir_url2 . '&name=' . $name2 . '">2</a>]
        </li>';
    }
    if (empty($files_intersect_final)) {
        echo '<li>Trống</li>';
    }
    echo '</ul>';
    
    echo '</div>';
}

echo '<div class="title">Chức năng</div>
<ul class="list">
    <li><img src="icon/list.png"/> <a href="index.php">Danh sách</a></li>
</ul>';

require_once 'footer.php';
