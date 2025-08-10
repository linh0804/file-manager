<?php
namespace app;

function zipDir($path, $file, $isDelete = false)
{
    if (@is_file($file)) {
        @unlink($file);
    }

    $zip = new Zip();

    if ($zip->open($file, ZipArchive::CREATE) === true) {
        $path = realpath($path);
        $files = readFullDir($path);

        foreach ($files as $name => $file) { 
            $filePath = $file->getRealPath();          
            $zip->add($filePath, $path . DIRECTORY_SEPARATOR);        
        }

        $zip->close();

        if ($isDelete) {
            removeDir($path);  
        }

        return true;
    }

    return false;
}

function printFileActions(SplFileInfo $file)
{
    global $pages, $formats, $dirEncode;

    $path = $file->getPathname();
    $name = $file->getFilename();
    $ext = $file->getExtension();

    echo '<div class="title">Chức năng</div>
    <div class="list">';

    if (in_array($ext, $formats['zip'])) {
        echo '<a href="file_viewzip.php?path=' . $path . $pages['paramater_1'] . '" class="button"><img src="icon/unzip.png"/> Xem</a>
          <a href="file_unzip.php?path=' . $path . $pages['paramater_1'] . '" class="button"><img src="icon/unzip.png"/> Giải nén</a> ';
    } elseif (isFormatText($name) || isFormatUnknown($name)) {
        echo '<a href="edit_text.php?path=' . base64_encode($path) . '" class="button"><img src="icon/edit.png"/> Sửa văn bản</a>
          <a href="edit_code.php?dir=' . $dirEncode . '&name=' . $name . '" class="button"><img src="icon/edit_text_line.png"/> Sửa code</a>
          <a href="edit_text_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" class="button"><img src="icon/edit_text_line.png"/> Sửa theo dòng</a>
          <a href="view_code.php?dir=' . $dirEncode . '&name=' . $name . '" class="button"><img src="icon/columns.png"/> Xem code</a> ';
    }

    echo '<a href="file.php?act=download&path=' . $path . $pages['paramater_1'] . '" class="button"><img src="icon/download.png"/> Tải về</a>    
        <a href="file.php?act=rename&path=' . $path . $pages['paramater_1'] . '" class="button"><img src="icon/rename.png"/> Đổi tên</a>
        <a href="file.php?act=copy&path=' . $path . $pages['paramater_1'] . '" class="button"><img src="icon/copy.png"/> Sao chép</a>
        <a href="file_move.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '" class="button"><img src="icon/move.png"/> Di chuyển</a>
        <a href="file.php?act=chmod&path=' . $path . $pages['paramater_1'] . '" class="button"><img src="icon/access.png"/> Chmod</a>
        <button onclick="fileAjaxDelete(this)" data-action="delete" data-path="' . htmlspecialchars($path) . '" class="button"><img src="icon/delete.png"/> Xóa</button>
        <a href="file.php?path=' . $path . $pages['paramater_1'] . '" class="button"><img src="icon/info.png"/> Thông tin</a>
    </div>';

    echo '<a href="index.php?path=' . dirname($path) . $pages['paramater_1'] . '" style="">
        <img src="icon/back.png"> 
        <strong class="back">Trở lại</strong>
    </a>';
}

function printFolderActions()
{
    global $name, $pages, $formats, $dirEncode;

    echo '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/zip.png"/> <a href="folder_zip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Nén zip</a></li>
        <li><img src="icon/rename.png"/> <a href="file.php?act=rename&path=' . $dirEncode . '%2F' . $name . $pages['paramater_1'] . '">Đổi tên</a></li>
        <li><img src="icon/copy.png"/> <a href="folder_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sao chép</a></li>
        <li><img src="icon/move.png"/> <a href="folder_move.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Di chuyển</a></li>
        <button onclick="fileAjaxDelete(this)" data-action="delete" data-path="' . $dirEncode . '%2F' . $name . '" class="button"><img src="icon/delete.png"/> Xóa</button>
        <li><img src="icon/list.png"/> <a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
    </ul>';

    echo '<a href="index.php?path=' . $dirEncode . $pages['paramater_1'] . '" style="">
        <img src="icon/back.png"> 
        <strong class="back">Trở lại</strong>
    </a>';
}

