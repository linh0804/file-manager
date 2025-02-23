<?php

const ACCESS = true;
const INDEX  = true;

require '.init.php';

if (!isLogin) {
    goURL('login.php');
}

$dir = !empty($_GET['dir']) ? rawurldecode($_GET['dir']) : cookie('fm_home', $_SERVER['DOCUMENT_ROOT']);
$dir = processDirectory($dir);
$title = 'Danh sách';
$dirEncode = rawurlencode($dir);

require 'header.php';

if (!file_exists($dir)) {
    echo '<div class="notice_failure">Đường dẫn không tồn tại!</div>';
    echo '<br><a href="javascript:history.back()" style="">
      <img src="icon/back.png"> 
      <strong class="back">Trở lại</strong>
    </a>';
    require 'footer.php';
    exit;
}

$lists = getListDirIndex($dir);
$count = count($lists);
$html  = printPath($dir, true);

echo '<script language="javascript" src="' . asset('js/checkbox.js') . '"></script>';
echo '<div class="title">' . $html . ' <span class="copyButton" data-copy="' . htmlspecialchars($dir) . '" style="color: pink">[copy]</span></div>';

if (isAppDir($dir)) {
    echo '<div class="notice_failure">Bạn đang xem thư mục của File Manager!</div>';
}

echo '<form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post" name="form">
<div class="list">';

if (preg_replace('|[a-zA-Z]+:|', '', str_replace('\\', '/', $dir)) != '/') {
    $path = strrchr($dir, '/');

    if ($path !== false) {
        $path = 'index.php?dir=' . rawurlencode(dirname($dir));
    } else {
        $path = 'index.php';
    }

    echo '
        <a href="' . $path . '">
            <img src="icon/back.png" style="margin-left: 5px; margin-right: 5px"/> 
            <strong class="back">...</strong>
        </a>
    <hr>';
}

if ($count <= 0) {
    echo '<img src="icon/empty.png"/> <span class="empty">Không có thư mục hoặc tập tin</span>';
} else {
    $start = 0;
    $end = $count;

    if ($configs['page_list'] > 0 && $count > $configs['page_list']) {
        $pages['total'] = ceil($count / $configs['page_list']);

        if ($pages['total'] <= 0 || $pages['current'] > $pages['total']) {
            goURL('index.php?dir=' . $dirEncode . ($pages['total'] <= 0 ? null : '&page_list=' . $pages['total']));
        }

        $start = ($pages['current'] * $configs['page_list']) - $configs['page_list'];
        $end   = $start + $configs['page_list'] >= $count ? $count : $start + $configs['page_list'];
    }
    
    echo '<div class="table-list-file"><table class="list-file">';

    for ($i = $start; $i < $end; ++$i) {
        $name  = $lists[$i]['name'];
        $path  = $dir . '/' . $name;
        $file = new SplFileInfo($path);
        $perms = getChmod($path);
        
        if ($lists[$i]['is_app_dir']) {
            $nameDisplay = '<i>' . $name . '</i>';
        } else {
            $nameDisplay = $name;
        }
        
        if ($file->isLink()) {
            $nameDisplay = '<span style="color:darkcyan">' . $nameDisplay . '</span>';
        }

        if ($lists[$i]['is_directory']) {            
            echo '<tr>
                <td><input type="checkbox" name="entry[]" value="' . $name . '"/></td>
                <td class="name"><b>' . getFileLink($path) . '</b></td>
                <td></td>
                <td><a href="folder_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" class="chmod">' . $perms . '</a></td>
            </tr>';
        } else {
            echo '<tr>
                <td><input type="checkbox" name="entry[]" value="' . $name . '"/></td>
                <td class="name">' . getFileLink($path) . '</td>
                <td><span class="size">' . size(@filesize($dir . '/' . $name)) . '</span></td>
                <td><a href="file_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" class="chmod">' . $perms . '</a></td>
            </tr>';
        }
    }
    
    echo '</table></div>';
    echo <<<'Z'
    <script>
        $("table.list-file tr").click(function () {
            $(this).addClass("active").siblings().removeClass("active");
        });
    </script>
    Z;
    
    echo '<hr><label><input id="file-select-all" type="checkbox" name="all" value="1" /> <strong class="form_checkbox_all">Chọn tất cả</strong></label>';

    if ($configs['page_list'] > 0 && $pages['total'] > 1) {
        echo '<hr>' . page($pages['current'], $pages['total'], array(PAGE_URL_DEFAULT => 'index.php?dir=' . $dirEncode, PAGE_URL_START => 'index.php?dir=' . $dirEncode . '&page_list='));
    }
}
?>

</div>

<div id="file-select-opt" class="list" style="display: none">
    <button name="option" value="0" class="button"><img src="icon/copy.png"/> Sao chép</button>
    <button name="option" value="1" class="button"><img src="icon/move.png"/> Di chuyển</button>
    <button name="option" value="3" class="button"><img src="icon/zip.png"/> Zip</button>
    <button name="option" value="2" class="button"><img src="icon/delete.png"/> Xoá</button>
    <button name="option" value="4" class="button"><img src="icon/access.png"/> Chmod</button>
    <button name="option" value="5" class="button"><img src="icon/rename.png"/> Đổi tên</button>
</div>

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
    <li><a href="create.php?dir=<?= $dirEncode . $pages['paramater_1'] ?>"><img src="icon/create.png"/> Tạo mới</a></li>
    <li><a href="upload.php?dir=<?= $dirEncode . $pages['paramater_1'] ?>"><img src="icon/upload.png"/> Tải lên</a></li>
    <li><a href="import.php?dir=<?= $dirEncode . $pages['paramater_1'] ?>"><img src="icon/import.png"/> Nhập khẩu</a></li>
    <li><a href="find_in_folder.php?dir=<?= $dirEncode ?>"><img src="icon/search.png"/> Tìm trong thư mục</a></li>
    <li><a href="scan_error_log.php?dir=<?= $dirEncode ?>"><img src="icon/search.png"/> Tìm <b style="color:red">error_log</b></a></li>
    <li><a href="#" class="copyButton" data-copy="<?= baseUrl . '/webdav.php/' . ltrim(htmlspecialchars($dir), '/') ?>">&bull; Webdav</a></li>
    <li>
        <details>
            <summary>-- Thư mục hiện tại --</summary>
            <hr>
            <a href="file.php?path=<?= $dir ?>" class="button"><img src="icon/info.png"/> Thông tin</a>
            <a href="file.php?act=rename&path=<?= $dir . $pages['paramater_1'] ?>" class="button"><img src="icon/rename.png"/> Đổi tên</a>
            <a href="folder_zip.php?dir=<?= dirname($dir) . '&name=' . basename($dir) . $pages['paramater_1'] ?>" class="button"><img src="icon/zip.png"/> Nén zip</a>
            <a href="folder_copy.php?dir=<?= dirname($dir) . '&name=' . basename($dir) . $pages['paramater_1'] ?>" class="button"><img src="icon/copy.png"/> Sao chép</a>
            <a href="folder_move.php?dir=<?= dirname($dir) . '&name=' . basename($dir) . $pages['paramater_1'] ?>" class="button"><img src="icon/move.png"/> Di chuyển</a>
            <a href="folder_chmod.php?dir=<?= dirname($dir) . '&name=' . basename($dir) . $pages['paramater_1'] ?>" class="button"><img src="icon/access.png"/> Chmod</a>
            <button onclick="fileAjaxDelete(this)" data-action="delete" data-path="<?= htmlspecialchars($dir) ?>" class="button"><img src="icon/delete.png"/> Xóa</button>
        </details>
    </li>
</ul>

<?php require 'footer.php' ?>