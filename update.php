<?php

define('ACCESS', true);
define('alwaysCheckUpdate', true);

require_once 'function.php';
require_once 'update.class.php';

define('FORMATS', $formats);

function remove_dir($dir = null)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") {
                    remove_dir($dir."/".$object);
                } else {
                    unlink($dir."/".$object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

$title = 'Cập nhật';

require_once 'header.php';

$zip = new ZipArchive();
$update = new Update();
$thisver = __DIR__ .'/tmp/thisversion';

echo '<div class="title">' . $title . '</div>';

$remoteVersion = getNewVersion();

if ($remoteVersion === false) {
    echo '<div class="list">Lỗi máy chủ cập nhật!</div>';
    require_once 'footer.php';
    exit();
}

if (isset($_POST['submit'])) {
    if (
        !isset($_POST['token'])
        || !isset($_SESSION['token'])
        || $_POST['token'] != $_SESSION['token']
    ) {
        unset($_SESSION['token']);
        goURL('update.php');
    }

    if(!isset($_POST['select']) && !isset($_POST['all'])) {
        echo '<div class="list">Lựa chọn không chính xác!</div>';
    }

    if(isset($_POST['select']) && !isset($_POST['all'])) {
        $select = $_POST['select'];
        echo '<div class="list">';
        foreach ($select as $value) {
            $name = explode('/', $value);
            $lastElement = array_pop($name);
            $folder = __DIR__ .'/tmp/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER . str_replace('/'. $lastElement, '', $value);
            $save = __DIR__ . str_replace('/'. $lastElement, '', $value);
            if($update->exec($lastElement, $folder, $save)) {
                echo $lastElement . ' đã được cập nhật!<hr />';
            } else {
                echo  $lastElement . ' không được cập nhật!<hr />';
            }
        }
        echo '<a style="color:blue" href="index.php">Trang chủ</a></div>';
    }

    if(isset($_POST['all'])) {
        mergeFolder(__DIR__ . '/tmp/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER, __DIR__);
        @remove_dir(__DIR__ .'/tmp/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER);
        @remove_dir($thisver .'/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER);

        $file = 'manager-' . time() . '.zip';
        import(REMOTE_FILE, $file);
        if ($zip->open($file) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileInfo = $zip->statIndex($i);
                $filename = $fileInfo['name'];
                $fileInExtractPath = $thisver . '/' . $filename;
                if (file_exists($fileInExtractPath)) {
                    $zipFileTime = $fileInfo['mtime'];
                    $localFileTime = filemtime($fileInExtractPath);
                    if ($zipFileTime > $localFileTime) {
                        $zip->extractTo($extractPath, $filename);
                    }
                } else {
                    $zip->extractTo($extractPath, $filename);
                }
            }
            $zip->close();
            if (
                unlink($file)
                && @rename($thisver .'/'. REMOTE_DIR_IN_ZIP, $thisver .'/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER)
            ) {
                goURL('index.php');
            }
        }
    }
} else {
    @remove_dir(__DIR__ .'/tmp/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER);

    if (!hasNewVersion()) {
        echo '<div class="list">
            Bạn đang sử dụng phiên bản manager mới nhất!<br />
        </div>';
    }

    $file = 'manager-' . time() . '.zip';

    if (!isset($_POST['submit']) && import(REMOTE_FILE, $file)) {
        if ($zip->open($file) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $fileInfo = $zip->statIndex($i);
                $filename = $fileInfo['name'];
                $fileInExtractPath = __DIR__ .'/tmp/' . $filename;
                if (file_exists($fileInExtractPath)) {
                    $zipFileTime = $fileInfo['mtime'];
                    $localFileTime = filemtime($fileInExtractPath);
                    if ($zipFileTime > $localFileTime) {
                        $zip->extractTo($extractPath, $filename);
                    }
                } else {
                    $zip->extractTo($extractPath, $filename);
                }
            }
            $zip->close();
            @rename(__DIR__ .'/tmp/'. REMOTE_DIR_IN_ZIP, __DIR__ .'/tmp/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER);
            @unlink($file);
        } else {
            echo '<div class="list">Lỗi! Không thể cài đặt bản cập nhật</div>';
        }
    } else {
        echo '<div class="list">Lỗi! Không thể tải bản  cập nhật</div>';
    }

    $token = time();
    $_SESSION['token'] = $token;
    $old = __DIR__;
    $new = __DIR__ . '/tmp/'. NAME_DIRECTORY_INSTALL_FILE_MANAGER;

    echo '<div class="list" style="padding:5px;font-size:xsmall;">
        <details open="open">
        <summary><b>Bảng thông tin file update!</b></summary>
        <hr />';

    echo '<button class="button" value="0" name="hidden_file" id="hidden_file" onClick="javascript:hidden_same();">Mở file không update!</button><br />';

    echo '<form action="update.php" method="post">
        <input type="hidden" name="token" value="' . $token . '" />';

    if($remoteVersion['major'] . '.' . $remoteVersion['minor'] . '.' . $remoteVersion['patch'] !== VERSION_MAJOR .'.'. VERSION_MINOR .'.'. VERSION_PATCH) {
        echo '<input type="submit" name="submit" value="Cập nhật file đã chọn"/>';
    }

    echo '<table style="table-layout:fixed; width:100%; height:500px; white-space:nowrap; margin-top:0;">';
    echo '<tr>';

    echo '<td style="width: 50%; border-right:1px solid red;text-align:left;margin: 0; vertical-align: top;">';
    echo '<div style="overflow-x: scroll; height:100%; ">';
    echo '<b style="margin-left: 5px">Bản hiện tại</b><br /><br />';
    echo $update->compareAll($old, $new, 1);
    echo '</div>';
    echo '</td>';

    echo '<td style="width: 50%; border-left:1px solid red;text-align:left;margin: 0; vertical-align: top;">';
    echo '<div style="overflow-x: scroll; height:100%;">';
    echo '<b style="margin-left: 10px">Bản mới</b><br /><br />';
    echo $update->compareAll($new, $old, 2);
    echo '</div>';
    echo '</td>';

    echo '</tr>';
    echo '</table>';

    echo '</form>
        </details>
    </div>';

    echo '<script>
        function isHidden(e) {
            return e.style.display == "none";
        }

        function hiddenFileSame() {
            // an het file trung
            const fileSames = document.getElementsByClassName("fileSame");
            for (let i = 0; i < fileSames.length; i++) {
                fileSames[i].style.display = isHidden(fileSames[i]) ? "block" : "none";
            }
        }
        
        function updateFolder() {
            // lap qua cac folder
            const folder = document.getElementsByClassName("folder");

            for (let i = 0; i < folder.length; i++) {
                let allNone = true;

                // lap qua tat ca cac file
                // xem tat ca co an hay khong
                // co thi hien thu muc trong

                const files = folder[i].getElementsByClassName("file");

                for (let j = 0; j < files.length; j++) {
                    if (!isHidden(files[j])) {
                        allNone = false;
                        break;
                    }
                }
              
                // hien thu muc trong sau khi an file trung
                const empty = folder[i].querySelector(".emptys");
                if (empty) {
                    empty.style.display = allNone ? "block" : "none";
                }
            }
        }
        
        // fix thu muc trong        
        updateFolder();

        const hidden_same = () => {
            hiddenFileSame();
            updateFolder();
        
            // button
            var button = document.querySelector("#hidden_file");
            if (button.value === "0") {
                var elements = document.getElementsByClassName("emptys");

                Array.prototype.forEach.call(elements, function (e) {
                    e.style.display = "none";
                })
            }
        
            button.innerText = button.value === "1"
                ? "Mở file không update!"
                : "Đóng file không update!";

            button.value = button.value === "1" ? "0" : "1";
        }
    </script>';

    if ($remoteVersion['major'] . '.' . $remoteVersion['minor'] . '.' . $remoteVersion['patch'] !== VERSION_MAJOR .'.'. VERSION_MINOR .'.'. VERSION_PATCH) {
        echo '<div class="list">
            <span>Có phiên bản <b>' . $remoteVersion['major'] . '.' . $remoteVersion['minor'] . '.' . $remoteVersion['patch'] . '</b>, bạn có muốn cập nhật?</span><hr />
            <span>' . $remoteVersion['message'] . '</span><hr />
            <form action="update.php" method="post" name="form" onsubmit="return validateForm();">
                <input type="hidden" name="all" value="1" />
                <input type="hidden" name="token" value="' . $token . '" />
                <input type="submit" name="submit" value="Cập nhật tất cả"/>
            </form>
        </div>';

        echo '<script type="text/javascript">
            const validateForm = () => {
                if (window.confirm(\'Bạn có muốn cập nhật tất cả, có thể làm mất những gì bạn sửa trong manager!\')) {
                    return true;
                } else {
                    return false;                       
                }
            }               
        </script>';
    }
}

require_once 'footer.php';
