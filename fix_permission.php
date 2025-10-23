<?php
namespace app;

use ngatngay\http\request;

define('ACCESS', true);

require_once '_init.php';

$title = 'Sửa quyền file/thư mục';

require_once '_header.php';

echo '<style>
    input[type="text"] {
        width: 100%;
    }

    pre {
        padding: 6px;
        border: 0.5px solid #cecece;
        white-space: pre;
        overflow-x: scroll;
    }

    pre#output {
        overflow-x: scroll;
        white-space: pre;
    }
</style>';

echo '<div class="title">' . $title . '</div>';

echo '<div class="list">
   Trên hosting file .htaccess chmod không đúng sẽ không dùng được!<br />
   Công cụ này đã được sinh ra ^^!
</div>';

$folder = (string) request::post('folder', $dir);
$own = (string) request::post('own', get_current_user());
$folder_mode = (string) request::post('folder_mode', 755);
$file_mode = (string) request::post('file_mode', 644);

echo '<div class="list">';

echo '<form method="post">
    <span>Thư mục:</span><br />
    <input type="text" name="folder" value="' . htmlspecialchars($folder) . '" /><br />

    <span>User:</span><br />
    <input type="text" name="own" value="' . htmlspecialchars($own) . '" /><br />
    
    <span>Folder mode:</span><br />
    <input type="text" name="folder_mode" value="' . htmlspecialchars($folder_mode) . '" /><br />
    
    <span>File mode:</span><br />
    <input type="text" name="file_mode" value="' . htmlspecialchars($file_mode) . '" /><br />

   <input type="submit" name="submit" value="OK" />
</form>';

// OK
if (isset($_POST['submit'])) {
    echo '<hr />';

    echo 'Thư mục: ';
    echo '<pre style="white-space: pre-wrap">' . htmlspecialchars($folder) . '</pre>';

    $files = readFullDir($folder);
    $chown_fail = [];
    $file_fail = [];
	$folder_fail = [];
    
    foreach ($files as $file) {
        // chown
        if (!chown($file, $own)) {
            $chown_fail[] = $file;
        }
        
        if ($file->isDir()) {
            if (!chmod($file, intval($folder_mode, 8))) {
            	$folder_fail[] = $file;
        	}
		}

        if ($file->isFile()) {
            if (chmod($file, intval($file_mode, 8))) {
            	$file_fail[] = $file;
        	}
		}
    }
    
    echo '<hr />';
    echo 'Chown thất bại: ' . count($chown_fail);
    echo '<pre>' . implode('<br>', $chown_fail) . '</pre>';

    echo '<hr />';
    echo 'Chmod thư mục thất bại: ' . count($folder_fail);
    echo '<pre>' . implode('<br>', $folder_fail) . '</pre>';

    echo '<hr />';
    echo 'Chmod file thất bại: ' . count($file_fail);
    echo '<pre>' . implode('<br>', $file_fail) . '</pre>';
}

echo '</div>';

require_once '_footer.php';
