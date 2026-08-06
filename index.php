<?php

use Nightmare\Fs;

define('ACCESS', true);
require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$curr_path = $curr_path ?: config()->get('home');
$curr_path = $curr_path ?: getenv('HOME');
$curr_path = $curr_path ?: ($_SERVER['DOCUMENT_ROOT'] ?? '');
$curr_path = (string) $curr_path;

$page_list = isset($_GET['page_list']) ? intval($_GET['page_list']) : 1;
$page_list = $page_list < 1 ? 1 : $page_list;

$site_title = 'Danh sách - ' . basename($curr_path);

if (!isset($_GET['path'])) {
    redirect(act_link('index', ['path' => $curr_path]));
}

check_path($curr_path);

if (is_file($curr_path)) {
    redirect(act_link('file_info', ['path' => $curr_path]));
}

require SITE_HEADER;

?>

<div id="app-index-updater"></div>

<?php

echo '<div class="title">' . file_print_path($curr_path, true) . ' <span class="copy-button" data-copy="' . htmlspecialchars((string) $curr_path) . '" style="color: pink">[copy]</span></div>';

echo '<a href="' . act_link('index', ['path' => dirname($curr_path), 'page_list' => null]) . '">
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

    $entry_path = Fs::join_path($curr_path, $entry);

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

if (PAGE_SIZE <= 0) {
    $page_list = 1;
} elseif ($page_list > (int) ceil($count / PAGE_SIZE)) {
    $page_list = 1;
}

echo '<form data-modal action="' . get_curr_url_esc() . '" method="post" name="form">';

if ($count <= 0) {
    echo '<div class="list"><img src="icon/empty.png"/> <span class="empty">Không có thư mục hoặc tập tin</span></div>';
} else {
    $display_lists = paging_arr($lists, $page_list, PAGE_SIZE);

    echo '<div class="table-list-file"><table class="list-file">';

    foreach ($display_lists as $entry_path) {
        $file = new SplFileInfo($entry_path);
        $name = $file->getFilename();
        $perms = file_get_chmod($file->getPathname());

        echo '<tr>';
        echo '<td><input type="checkbox" name="entries[]" value="' . $name . '"/></td>';
        
        if ($file->isDir()) {
            echo '<td class="name"><b>' . file_get_display_link($file) . '</b></td>';
            echo '<td><span data-act="calc" data-path="' . $file->getPathname() . '" class="btn-calc-size size">[...]</span></td>';
        } else {
            echo '<td class="name">' . file_get_display_link($file) . '</td>';
            echo '<td><span class="size">' . Fs::sizen($file->getSize()) . '</span></td>';
        }

        echo '<td class="chmod">' . Fs::get_owner_name_by_id($file->getOwner()) . '</td>';
        echo '<td><a href="' . act_link('file_chmod', ['path' => $file->getPathname()]) . '" class="chmod">' . $perms . '</a></td>';
        echo '</tr>';
    }
    
    echo '<tr>
        <td><input id="file-select-all" type="checkbox" name="all" value="0" /></td>
        <td colspan="4"><b>Tổng: ' . $count .'</b></td>
    </tr>';

    echo '</table>';
    echo '</div>';

    echo '<div id="file-select-opt" class="list">
        <button class="button" type="button">(<span id="file-select-opt-count">0</span>)</button>

        <button formaction="' . act_link('multi_copy', ['path' => $curr_path]) . '" class="button"><img src="icon/copy.png"/> Sao chép</button>
        <button formaction="' . act_link('multi_move', ['path' => $curr_path]) . '" class="button"><img src="icon/move.png"/> Di chuyển</button>
        <button formaction="' . act_link('multi_zip', ['path' => $curr_path]) . '" class="button"><img src="icon/zip.png"/> Zip</button>
        <button formaction="' . act_link('multi_delete', ['path' => $curr_path]) . '" class="button"><img src="icon/delete.png"/> Xoá</button>
        <button formaction="' . act_link('multi_chmod', ['path' => $curr_path]) . '" class="button"><img src="icon/access.png"/> Chmod</button>
        <button formaction="' . act_link('multi_rename', ['path' => $curr_path]) . '" class="button"><img src="icon/rename.png"/> Đổi tên</button>
    </div>';

    echo '<div class="list">';
    echo paging('index', 'page_list', ['path' => $curr_path], $page_list, $count, PAGE_SIZE);
    echo '</div>';
}
?>

<script>
    $("table.list-file tr").click(function () {
        $(this).addClass("active").siblings().removeClass("active");
    });

    function updateFileSelectOpt() {
        var selectedEntries = $('input[name="entries[]"]:checked').length;
        $('#file-select-opt-count').text(selectedEntries);
    }

    $('input[name="entries[]"]').on('change', function () {
        updateFileSelectOpt();
    });

    $('#file-select-all').on('change', function () {
        $('input[name="entries[]"]').prop('checked', this.checked);
        updateFileSelectOpt();
    });

    updateFileSelectOpt();
</script>

</form>

<div class="title">Chức năng</div>

<ul class="list">
    <li><a data-modal href="<?= act_link('file_create', ['path' => $curr_path]) ?>"><img src="icon/create.png"/> Tạo mới</a></li>
    <li><a href="<?= act_link('file_upload', ['path' => $curr_path]) ?>"><img src="icon/upload.png"/> Tải lên</a></li>
    <li><a href="<?= act_link('file_import', ['path' => $curr_path]) ?>"><img src="icon/import.png"/> Nhập khẩu</a></li>
    <li><a href="<?= act_link('file_find_in_folder', ['path' => $curr_path]) ?>"><img src="icon/search.png"/> Tìm trong thư mục</a></li>
    <li><a href="webdav.php/<?= ltrim($curr_path, '/') ?>"><img src="icon/rows.png"/> Webdav</a></li>
    <li><a href="<?= act_link('file_info', ['path' => $curr_path]) ?>"><img src="icon/info.png"/> Thông tin</a></li>
</ul>

<?php require SITE_FOOTER ?>
