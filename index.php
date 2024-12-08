<?php

const ACCESS = true;
const INDEX  = true;

require '.init.php';

if (!IS_LOGIN) {
    goURL('login.php');
}

$dir = !empty($_GET['dir']) ? rawurldecode($_GET['dir']) : cookie('fm_home', $_SERVER['DOCUMENT_ROOT']);
$dir = processDirectory($dir);
$title = 'Danh sách';
$handler = null;

require 'header.php';

// load file list
$handler = @scandir($dir);

if (!is_array($handler)) {
    $handler = [];
}

$dirEncode = rawurlencode($dir);
$count = count($handler);
$lists = [];

if ($count > 0) {
    $folders = [];
    $files   = [];

    foreach ($handler as $entry) {
        if ($entry != '.' && $entry != '..') {
            //if ($entry == DIRECTORY_FILE_MANAGER && IS_ACCESS_PARENT_PATH_FILE_MANAGER) {
            /* Is hide directory File Manager */
            //} else

            if (is_dir($dir . '/' . $entry)) {
                $folders[] = $entry;
            } else {
                $files[] = $entry;
            }
        }
    }

    if (count($folders) > 0) {
        sortNatural($folders);

        foreach ($folders as $entry) {
            $lists[] = [
                'name' => $entry,
                'is_directory' => true,
                'is_app_dir' => isAppDir($dir . '/' . $entry)
            ];
        }
    }

    if (count($files) > 0) {
        sortNatural($files);

        foreach ($files as $entry) {
            $lists[] = [
                'name' => $entry,
                'is_directory' => false,
                'is_app_dir' => isAppDir($dir . '/' . $entry)
            ];
        }
    }
}

$count = count($lists);
$html  = printPath($dir, true);

echo '<script language="javascript" src="' . asset('js/checkbox.js') . '"></script>';
echo '<div class="title">' . $html . ' <span class="copyButton" data-copy="' . htmlspecialchars($dir) . '" style="color: pink">[copy]</span></div>';

if (isAppDir($dir)) {
    echo '<div class="notice_failure">Bạn đang xem thư mục của File Manager!</div>';
}

echo '<form action="action.php?dir=' . $dirEncode . $pages['paramater_1'] . '" method="post" name="form"><ul class="list_file">';

if (preg_replace('|[a-zA-Z]+:|', '', str_replace('\\', '/', $dir)) != '/') {
    $path = strrchr($dir, '/');

    if ($path !== false) {
        $path = 'index.php?dir=' . rawurlencode(dirname($dir));
    } else {
        $path = 'index.php';
    }

    echo '<li class="normal">
        <a href="' . $path . '">
            <img src="icon/back.png" style="margin-left: 5px; margin-right: 5px"/> 
            <strong class="back">...</strong>
        </a>
    </li>';
}

if ($count <= 0) {
    echo '<li class="normal"><img src="icon/empty.png"/> <span class="empty">Không có thư mục hoặc tập tin</span></li>';
} else {
    $start = 0;
    $end   = $count;

    if ($configs['page_list'] > 0 && $count > $configs['page_list']) {
        $pages['total'] = ceil($count / $configs['page_list']);

        if ($pages['total'] <= 0 || $pages['current'] > $pages['total']) {
            goURL('index.php?dir=' . $dirEncode . ($pages['total'] <= 0 ? null : '&page_list=' . $pages['total']));
        }

        $start = ($pages['current'] * $configs['page_list']) - $configs['page_list'];
        $end   = $start + $configs['page_list'] >= $count ? $count : $start + $configs['page_list'];
    }

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

        if ($lists[$i]['is_directory']) {            
            echo '<li class="folder">
                <div>
                    <input type="checkbox" name="entry[]" value="' . $name . '"/>
                    <a href="folder_edit.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">' . getIcon('folder', $name) . '</a>
                    <a href="index.php?dir=' . rawurlencode($path) . '">' . $nameDisplay . '</a>
                    <div class="perms">
                        <a href="folder_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" class="chmod">' . $perms . '</a>
                    </div>
                </div>
            </li>';
        } else {
            $edit   = array(null, '</a>');
            $isEdit = false;

            if (in_array($file->getExtension(), $formats['text'])) {
                $isEdit = true;
            } elseif (in_array(strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name), $formats['source'])) {
                $isEdit = true;
            } elseif (isFormatUnknown($name)) {
                $isEdit = true;
            }

            if (strtolower($name) == 'error_log' || $isEdit) {
                $edit[0] = '<a href="edit_text.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">';
            } elseif (in_array($file->getExtension(), $formats['zip'])) {
                $edit[0] = '<a href="file_unzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">';
            } else {
                $edit[0] = '<a href="file_rename.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">';
            }

            echo '<li class="file">
                <p>
                    <input type="checkbox" name="entry[]" value="' . $name . '"/>
                    ' . $edit[0] . getIcon('file', $name) . $edit[1] . '
                    <a href="file.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">' . $nameDisplay . '</a>
                </p>
                <p>
                    <span class="size">' . size(@filesize($dir . '/' . $name)) . '</span>,
                    <a href="file_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" class="chmod">' . $perms . '</a>
                </p>
            </li>';
        }
    }

    echo '<li class="normal"><input type="checkbox" name="all" value="1" onClick="javascript:onCheckItem();"/> <strong class="form_checkbox_all">Chọn tất cả</strong></li>';

    if ($configs['page_list'] > 0 && $pages['total'] > 1) {
        echo '<li class="normal">' . page($pages['current'], $pages['total'], array(PAGE_URL_DEFAULT => 'index.php?dir=' . $dirEncode, PAGE_URL_START => 'index.php?dir=' . $dirEncode . '&page_list=')) . '</li>';
    }
}

echo '</ul>';

if ($count > 0) {
    echo '<div class="list">
        <button name="option" value="0" class="button"><img src="icon/copy.png"/> Sao chép</button>
        <button name="option" value="1" class="button"><img src="icon/move.png"/> Di chuyển</button>
        <button name="option" value="3" class="button"><img src="icon/zip.png"/> Zip</button>
        <button name="option" value="2" class="button"><img src="icon/delete.png"/> Xoá</button>
        <button name="option" value="4" class="button"><img src="icon/access.png"/> Chmod</button>
        <button name="option" value="5" class="button"><img src="icon/rename.png"/> Đổi tên</button>
    </div>';
}

echo '</form>';

echo '<div class="title">Chức năng</div>
<div class="list">
    <a href="create.php?dir=' . $dirEncode . $pages['paramater_1'] . '" class="button"><img src="icon/create.png"/> Tạo mới</a>
    <a href="upload.php?dir=' . $dirEncode . $pages['paramater_1'] . '" class="button"><img src="icon/upload.png"/> Tải lên tập tin</a>
    <a href="import.php?dir=' . $dirEncode . $pages['paramater_1'] . '" class="button"><img src="icon/import.png"/> Nhập khẩu tập tin</a>
    <a href="find_in_folder.php?dir=' . $dirEncode . '" class="button"><img src="icon/search.png"/> Tìm trong thư mục</a>
    <a href="scan_error_log.php?dir=' . $dirEncode . '" class="button"><img src="icon/search.png"/> Tìm <b style="color:red">error_log</b></a>
    <a href="#" class="button copyButton" data-copy="' . baseUrl . '/webdav.php/' . ltrim(htmlspecialchars($dir), '/') . '">&bull; Webdav</a>
    <a href="folder_detail.php?dir=' . $dirEncode . '" class="button"><img src="icon/info.png"/> Thông tin</a>
</div>';

require 'footer.php';

