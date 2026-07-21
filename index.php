<?php

use nightmare\fs;

define('ACCESS', true);

require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$curr_path = $curr_path ? $curr_path : config()->get('home', $_SERVER['DOCUMENT_ROOT']);
$curr_path = (string) $curr_path;
$site_title = 'Danh sách - ' . basename($curr_path);

if (!isset($_GET['path'])) {
    redirect(action_link('index', ['path' => $curr_path]));
}

check_path($curr_path);

if (is_file($curr_path)) {
    redirect(action_link('file', ['act' => 'info', 'path' => $curr_path]));
}

require SITE_HEADER;

?>
<style>
    div.title,
    div.title a,
    div.title .path_entry,
    div.title .path_seperator {
        overflow-wrap: anywhere;
        word-break: break-word;
    }
</style>

<div id="app-index-updater"></div>

<?php

echo '<div class="title">' . file_print_path($curr_path, true) . ' <span class="copy-button" data-copy="' . htmlspecialchars((string) $curr_path) . '" style="color: pink">[copy]</span></div>';

echo '<a href="' . action_link('index', ['path' => dirname($curr_path)]) . '">
  <div class="list">
    <img src="icon/back.png" style="margin-left: 5px; margin-right: 5px"/> 
    <strong class="back">...</strong>
  </div>
</a>';

if (is_app_file($curr_path)) {
    echo '<div class="notice_failure">Bạn đang xem thư mục của File Manager!</div>';
}

// list
$handler = @scandir($curr_path, SCANDIR_SORT_NONE);

if (!is_array($handler)) {
    $handler = [];
}

$lists = [];
$folders = [];
$files = [];

foreach ($handler as $entry) {
    if ($entry === '.' || $entry === '..') {
        continue;
    }

    $entry_path = fs::join_path($curr_path, $entry);

    if (is_dir($entry_path)) {
        $folders[] = $entry_path;
    } else {
        $files[] = $entry_path;
    }
}

sort_natural($folders);
sort_natural($files);

$lists = array_merge($folders, $files);
$count = count($lists);

echo '<form action="" method="post" name="form">';

if ($count <= 0) {
    echo '<div class="list"><img src="icon/empty.png"/> <span class="empty">Không có thư mục hoặc tập tin</span></div>';
} else {
    $start = 0;
    $end = $count;

    if (PAGE_SIZE > 0 && $count > PAGE_SIZE) {
        $pages['total'] = ceil($count / PAGE_SIZE);

        if ($pages['total'] <= 0 || $pages['current'] > $pages['total']) {
            redirect(action_link('index', [
                'path' => $curr_path,
                'page_list' => $pages['total'] <= 0 ? null : $pages['total'],
            ]));
        }

        $start = ($pages['current'] * PAGE_SIZE) - PAGE_SIZE;
        $end   = $start + PAGE_SIZE >= $count ? $count : $start + PAGE_SIZE;
    }

    echo '<div class="table-list-file"><table class="list-file">';

    for ($i = $start; $i < $end; ++$i) {
        $file = new SplFileInfo($lists[$i]);
        $name = $file->getFilename();
        $perms = file_get_chmod($file->getPathname());

        if ($file->isDir()) {
            echo '<tr>
                <td><input type="checkbox" name="entries[]" value="' . $name . '"/></td>
                <td class="name"><b>' . file_get_display_link($file) . '</b></td>
                <td><span data-act="calc" data-path="' . $file->getPathname() . '" class="btn-calc-size size">[...]</span></td>
                <td class="chmod">' . fs::get_owner_name_by_id($file->getOwner()) . '</td>
                <td><a href="' . action_link('file', ['act' => 'chmod', 'path' => $file->getPathname()]) . '" class="chmod">' . $perms . '</a></td>
            </tr>';
        } else {
            echo '<tr>
                <td><input type="checkbox" name="entries[]" value="' . $name . '"/></td>
                <td class="name">' . file_get_display_link($file) . '</td>
                <td><span class="size">' . fs::readable_size($file->getSize()) . '</span></td>
                <td class="chmod">' . fs::get_owner_name_by_id($file->getOwner()) . '</td>
                <td><a href="' . action_link('file', ['act' => 'chmod', 'path' => $file->getPathname()]) . '" class="chmod">' . $perms . '</a></td>
            </tr>';
        }
    }
    
    echo '<tr>
        <td><input id="file-select-all" type="checkbox" name="all" value="1" /></td>
        <td><b><i>Total: ' . $count .'</i></b></td>
    </tr>';

    echo '</table></div>';

    echo '<div class="list">';
    echo '<div id="file-select-opt" style="display: block">
        <button formaction="' . action_link('multi', ['act' => 'copy', 'path' => $curr_path]) . '" class="button"><img src="icon/copy.png"/> Sao chép</button>
        <button formaction="' . action_link('multi', ['act' => 'move', 'path' => $curr_path]) . '" class="button"><img src="icon/move.png"/> Di chuyển</button>
        <button formaction="' . action_link('multi', ['act' => 'zip', 'path' => $curr_path]) . '" class="button"><img src="icon/zip.png"/> Zip</button>
        <button formaction="' . action_link('multi', ['act' => 'delete', 'path' => $curr_path]) . '" class="button"><img src="icon/delete.png"/> Xoá</button>
        <button formaction="' . action_link('multi', ['act' => 'chmod', 'path' => $curr_path]) . '" class="button"><img src="icon/access.png"/> Chmod</button>
        <button formaction="' . action_link('multi', ['act' => 'rename', 'path' => $curr_path]) . '" class="button"><img src="icon/rename.png"/> Đổi tên</button>
    </div>';

    if (PAGE_SIZE > 0 && $pages['total'] > 1) {
        echo '<hr>' . page($pages['current'], $pages['total'], array(PAGE_URL_DEFAULT => action_link('index', ['path' => $curr_path]), PAGE_URL_START => action_link('index', ['path' => $curr_path]) . '&page_list='));
    }

    echo '</div>';
}
?>

<script>
    $("table.list-file tr").click(function () {
        $(this).addClass("active").siblings().removeClass("active");
    });

    $('#file-select-all').on('change', function () {
        for (let i = 0; i < document.form.elements.length; ++i) {
            if (document.form.elements[i].type === "checkbox") {
                document.form.elements[i].checked = document.form.all.checked === true;
            }
        }
    });
</script>

</form>

<div class="title">Chức năng</div>

<ul class="list">
    <li><a href="<?= action_link('file', ['act' => 'create', 'path' => $curr_path]) ?>"><img src="icon/create.png"/> Tạo mới</a></li>
    <li><a href="<?= action_link('file', ['act' => 'upload', 'path' => $curr_path]) ?>"><img src="icon/upload.png"/> Tải lên</a></li>
    <li><a href="<?= action_link('file', ['act' => 'import', 'path' => $curr_path]) ?>"><img src="icon/import.png"/> Nhập khẩu</a></li>
    <li><a href="<?= action_link('file', ['act' => 'find_in_folder', 'path' => $curr_path]) ?>"><img src="icon/search.png"/> Tìm trong thư mục</a></li>
    <li><a href="webdav.php/<?= ltrim($curr_path, '/') ?>"><img src="icon/rows.png"/> Webdav</a></li>
    <li><a href="<?= action_link('file', ['act' => 'info', 'path' => $curr_path]) ?>"><img src="icon/info.png"/> Thông tin</a></li>
</ul>

<?php require SITE_FOOTER ?>
