<?php
namespace app;

use ngatngay\fs;
use SplFileInfo;

define('ACCESS', true);
define('INDEX', true);

require '_init.php';

if (!isLogin) {
    goURL('login.php');
}

$path = isset($_GET['path']) ? $path : config()->get('home', $_SERVER['DOCUMENT_ROOT']);
$title = 'Danh sách - ' . basename((string) $path);

if (!isset($_GET['path'])) {
    goURL('index.php?path=' . $path);
}

check_path($path);

require 'header.php';

echo '<div class="title">' . printPath($path, true) . ' <span class="copyButton" data-copy="' . htmlspecialchars((string) $path) . '" style="color: pink">[copy]</span></div>';

echo '<a href="index.php?path=' . dirname((string) $path) . '">
  <div class="list">
    <img src="icon/back.png" style="margin-left: 5px; margin-right: 5px"/> 
    <strong class="back">...</strong>
  </div>
</a>';

if (isAppDir($path)) {
    echo '<div class="notice_failure">Bạn đang xem thư mục của File Manager!</div>';
}

// list
$handler = @scandir($path, SCANDIR_SORT_NONE);

if (!is_array($handler)) {
    $handler = [];
}

$lists = [];
$folders = [];
$files = [];

foreach ($handler as $entry) {
    if ($entry == '.' || $entry == '..') {
        continue;
    }

    $entry_path = fs::join_path($path, $entry);

    if (is_dir($entry_path)) {
        $folders[] = $entry_path;
    } else {
        $files[] = $entry_path;
    }
}

sortNatural($folders);
sortNatural($files);

$lists = array_merge($folders, $files);
$count = count($lists);

echo '<form action="action.php?dir=' . $path . $pages['paramater_1'] . '" method="post" name="form">';

if ($count <= 0) {
    echo '<div class="list"><img src="icon/empty.png"/> <span class="empty">Không có thư mục hoặc tập tin</span></div>';
} else {
    $start = 0;
    $end = $count;

    if ($configs['page_list'] > 0 && $count > $configs['page_list']) {
        $pages['total'] = ceil($count / $configs['page_list']);

        if ($pages['total'] <= 0 || $pages['current'] > $pages['total']) {
            goURL('index.php?path=' . $path . ($pages['total'] <= 0 ? null : '&page_list=' . $pages['total']));
        }

        $start = ($pages['current'] * $configs['page_list']) - $configs['page_list'];
        $end   = $start + $configs['page_list'] >= $count ? $count : $start + $configs['page_list'];
    }

    echo '<div class="table-list-file"><table class="list-file">';

    for ($i = $start; $i < $end; ++$i) {
        $file = new SplFileInfo($lists[$i]);
        $name = $file->getFilename();
        $perms = getChmod($file->getPathname());

        if (isAppDir($file->getPathname())) {
            $nameDisplay = '<i>' . $name . '</i>';
        } else {
            $nameDisplay = $name;
        }

        if ($file->isLink()) {
            $nameDisplay = '<span style="color:darkcyan">' . $nameDisplay . '</span>';
        }

        if ($file->isDir()) {
            echo '<tr>
                <td><input type="checkbox" name="entry[]" value="' . $name . '"/></td>
                <td class="name"><b>' . getFileLink($file->getPathname()) . '</b></td>
                <td><span data-action="calc" data-path="' . $file->getPathname() . '" class="btn-calc-size size">[...]</span></td>
                <td>' . fs::get_owner_name_by_id($file->getOwner()) . '</td>
                <td><a href="file.php?act=chmod&path=' . $file->getPathname() . $pages['paramater_1'] . '" class="chmod">' . $perms . '</a></td>
            </tr>';
        } else {
            echo '<tr>
                <td><input type="checkbox" name="entry[]" value="' . $name . '"/></td>
                <td class="name">' . getFileLink($file->getPathname()) . '</td>
                <td><span class="size">' . fs::readable_size($file->getSize()) . '</span></td>
                <td>' . fs::get_owner_name_by_id($file->getOwner()) . '</td>
                <td><a href="file.php?act=chmod&path=' . $file->getPathname() . $pages['paramater_1'] . '" class="chmod">' . $perms . '</a></td>
            </tr>';
        }
    }
    
    echo '<tr>
        <td><input id="file-select-all" type="checkbox" name="all" value="1" /></td>
        <td><b><i>Total: ' . $count .'</i></b></td>
    </tr>';

    echo '</table></div>';
    echo <<<'Z'
    <script>
        $("table.list-file tr").click(function () {
            $(this).addClass("active").siblings().removeClass("active");
        });
    </script>
    Z;

    echo '<div class="list">';
    echo '<div id="file-select-opt" style="display: none">
        <button name="option" value="0" class="button"><img src="icon/copy.png"/> Sao chép</button>
        <button name="option" value="1" class="button"><img src="icon/move.png"/> Di chuyển</button>
        <button name="option" value="3" class="button"><img src="icon/zip.png"/> Zip</button>
        <button name="option" value="2" class="button"><img src="icon/delete.png"/> Xoá</button>
        <button name="option" value="4" class="button"><img src="icon/access.png"/> Chmod</button>
        <button name="option" value="5" class="button"><img src="icon/rename.png"/> Đổi tên</button>
    </div>';

    if ($configs['page_list'] > 0 && $pages['total'] > 1) {
        echo '<hr>' . page($pages['current'], $pages['total'], array(PAGE_URL_DEFAULT => 'index.php?path=' . $path, PAGE_URL_START => 'index.php?path=' . $path . '&page_list='));
    }

    echo '</div>';
}
?>

<script>
    $('#file-select-all').on('change', function () {
        onCheckItem();

        if (this.checked) {
            $('#file-select-opt').show();
        } else {
            $('#file-select-opt').hide();
        }
    });

    $('input[name="entry[]"]').on('change', function() {
        if ($('input[name="entry[]"]:checked').length > 0) {
            $('#file-select-opt').show();
        } else {
           $('#file-select-opt').hide();
           $('#file-select-all').prop('checked', false);
        }
    });
</script>

</form>

<div class="title">Chức năng</div>

<ul class="list">
    <li><a href="create.php?path=<?= $path . '&' . referer_qs ?>"><img src="icon/create.png"/> Tạo mới</a></li>
    <li><a href="upload.php?path=<?= $path . '&' . referer_qs ?>"><img src="icon/upload.png"/> Tải lên</a></li>
    <li><a href="import.php?dir=<?= $path . '&' . referer_qs ?>"><img src="icon/import.png"/> Nhập khẩu</a></li>
    <li><a href="find_in_folder.php?path=<?= $path . '&' . referer_qs ?>"><img src="icon/search.png"/> Tìm trong thư mục</a></li>
    <li><a href="scan_error_log.php?dir=<?= $path . '&' . referer_qs ?>"><img src="icon/search.png"/> Tìm <b style="color:red">error_log</b></a></li>
    <li><a href="#" class="copyButton" data-copy="<?= baseUrl . '/webdav.php/' . ltrim(htmlspecialchars((string) $path), '/') ?>">&bull; Webdav</a></li>
    <li><img src="icon/info.png"/> <a href="file.php?path=<?= $path ?>">Thông tin</a></li>
</ul>

<?php require 'footer.php' ?>
