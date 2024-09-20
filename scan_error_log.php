<?php

define('ACCESS', true);

include_once 'function.php';

$title = 'Tìm error_log';

require_once 'header.php';

echo '<div class="title">' . $title . '</div>';

echo '<style>
	ul.info > li {
		white-space: normal !important;
	}
</style>';

if (
    $dir == null
    || !is_dir(processDirectory($dir))
) {
    echo '<div class="list"><span>Đường dẫn không tồn tại</span></div>
    <div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php' . $pages['paramater_0'] . '">Danh sách</a></li>
    </ul>';
} else {
    $dir = processDirectory($dir);

    echo '<div class="list">';
    echo '<span>' . printPath($dir, true) . '</span>';
    echo '</div>';

    echo '<div class="title">Danh sách error_log</div>';
    
    $have_error = false;
    $files = readDirectoryIterator($dir, [
        'vendor/',
        'node_modules/'
    ]);
    
    foreach ($files as $file) {
        if (!$file->getSize()) {
            continue;
        }
        
        if ($file->getFilename() !== 'error_log') {
            continue;
        }
        
        if (!$have_error) {
            echo '<ul class="info">';
        }
        
        echo '<li>
            <span class="bull">&bull;</span>
            <a style="color: red" href="file.php?dir=' . rawurlencode(dirname($file->getPathname())) . '&name=' . $file->getFilename() . '">'
            . htmlspecialchars(ltrim(
                str_replace_first($dir, '', $file->getPathname())
            , '/'))
            . '</a> (' . size($file->getSize()) . ')
            </li>';
        
        if (!$have_error) {
            $have_error = true;
        }
    }
           
    if ($have_error) {
        echo '</ul>';
    }
    
    if (!$have_error) {
        echo '<div class="list">Trống</div>';
    }
    
    echo '<div class="title">Chức năng</div>
    <ul class="list">
        <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
    </ul>';
}

require_once 'footer.php';
