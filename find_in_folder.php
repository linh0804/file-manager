<?php

define('ACCESS', true);

require '.init.php';

check_path($path);

$title = 'Tìm trong thư mục';
$dir = $path;
$search = isset($_POST['search']) ? $_POST['search'] : '';
$replace = isset($_POST['replace']) ? $_POST['replace'] : '';
$replaceCheck  = isset($_POST['replaceCheck'])  ? (bool) $_POST['replaceCheck']  : false;
$case = isset($_POST['case']) ? (bool) $_POST['case'] : false;
$only_dir  = isset($_POST['only_dir'])  ? (bool) $_POST['only_dir']  : false;
$only_file = isset($_POST['only_file']) ? (bool) $_POST['only_file'] : false;
$exclude = isset($_POST['exclude']) ? $_POST['exclude'] : $excludeDirDefault;

require 'header.php';

echo '<style>
#find_list {
    margin: 5px 0;
}

#find_list .item {
    border: 1px solid #eeeeee;
    margin-bottom: 10px;
}

#find_list .item-title {
    padding: 7px;
}

#find_list .item-content {
    padding-left: 7px;
    padding-right: 7px;
    padding-bottom: 0;
    background-color: #eeeeee;
}

#find_list .item-content .item-content-item {
    padding-top: 7px;
    padding-bottom: 7px;
    border-bottom: 1px dotted #dddddd;
    /* word-break: break-all !important; */
    overflow-x: auto !important;
}
</style>';

echo '<div class="title">' . $title . '</div>';

echo '<div class="list">
    <span>' . printPath($dir, true) . '</span><hr/>
    <form method="post">
        Nội dung tìm kiếm:<br />
        <input type="text" name="search" value="' . htmlspecialchars($search) . '" style="width: 80%" /><br />
        
        Thay thế:<br />
        <input type="text" name="replace" value="' . htmlspecialchars($replace) . '" style="width: 80%" /><br />

        <label>
        <input type="checkbox" name="case" ' . ($case ? 'checked="checked"' : '') . ' />
        Phân biệt chữ hoa<br />
        </label>
        
        <label>
        <input type="checkbox" name="only_dir" ' . ($only_dir ? 'checked="checked"' : '') . ' />
        Chỉ tìm tên thư mục<br />
        </label>
        
        <label>
        <input type="checkbox" name="only_file" ' . ($only_file ? 'checked="checked"' : '') . ' />
        Chỉ tìm tên file<br />
        </label>

        <label>
        <input type="checkbox" name="replaceCheck" />
        Thay thế<br><br>
        </label>

        Loại trừ theo biểu thức:<br />
        <textarea name="exclude" rows="5">' . htmlspecialchars($exclude) . '</textarea><br />
        <p style="font-size: small">
            VD: "vendor/", "system/vendor/", "style.css",...
        </p>
        <input type="submit" name="submit" value="Tìm kiếm"/>
    </form>
</div>';

if (isset($_POST['submit'])) {
    $error = false;
    $excludes = explode(PHP_EOL, $exclude);

    if (empty($search)) {
        echo $error = '<div class="notice_failure">Chưa nhập nội dung!</div>';
    }
    
    if ($error === false) {
        $files = readFullDir($dir, $excludes);
        $files_search_count = 0;

        echo '<div id="find_list">';

        foreach ($files as $file) {
            // lấy thông tin cần thiết
            $file_name = $file->getFilename();
            $file_path = $file->getPathname();
            $file_path = processDirectory($file_path);
            $file_path_sort = str_replace($dir, '', $file_path);
            $file_path_sort = ltrim($file_path_sort, '/');

            // xử lý loại tìm kiếm
            if ($only_dir) {
                $search = ltrim($search, '/');
                if (!$file->isDir()) {
                    continue;
                }
                
                // phân biệt chữ hoa
                if ($case) {
                    $haveSearch = strpos($file_path_sort, $search);
                } else {
                    $haveSearch = stripos($file_path_sort, $search);
                }

                if ($haveSearch !== false) {
                    // cộng 1 vào số file tìm được
                    $files_search_count += 1;

                    echo '<div class="item">';
                    echo '<div class="item-title">';
                    echo '<span class="bull">&bull;</span>
                        <a style="color: red" target="_blank" href="index.php?path=' . rawurlencode($file_path) . '">'
                            . htmlspecialchars($file_path_sort)
                        . '</a>';
                    echo '</div>';
                    echo '</div>';
                }

                continue;
            } else if ($only_file) {
                $search = ltrim($search, '/');
                if (!$file->isFile()) {
                    continue;
                }
                
                // phân biệt chữ hoa
                if ($case) {
                    $haveSearch = strpos($file_path_sort, $search);
                } else {
                    $haveSearch = stripos($file_path_sort, $search);
                }

                if ($haveSearch !== false) {
                    // cộng 1 vào số file tìm được
                    $files_search_count += 1;

                    echo '<div class="item">';
                    echo '<div class="item-title">';
                    echo '<span class="bull">&bull;</span>
                        <a style="color: red" href="file.php?path=' . $file_path . '">'
                        . htmlspecialchars($file_path_sort)
                    . '</a>';
                    echo '</div>';
                    echo '</div>';
                }
                
                continue;
            } else {
            	// tìm trong file
                if (!$file->isFile()) {
                    continue;
                }
                if (in_array($file->getExtension(), [
                    'mp3',
                    'mp4',
                    'flac',
                    'zip',
                    'phar'
                ])) {
                	continue;
                }
            }

            // đọc và tìm nội dung theo từng dòng
            $fileObj = $file->openFile();
            $file_have_search = false;
            $display = false;

            while (!$fileObj->eof()) {
                $line = $fileObj->fgets();
                $line_number = $fileObj->key();

                // phân biệt chữ hoa
                if ($case) {
                    $haveSearch = strpos($line, $search);
                } else {
                    $haveSearch = stripos($line, $search);
                }

                // tìm thấy
                if ($haveSearch !== false) {
                    if (!$display) {
                        $display = true;

                        // cộng 1 vào số file tìm được
                        $files_search_count += 1;

                        echo '<div class="item">';
                        echo '<div class="item-title">';
                        echo '<span class="bull">&bull;</span>
                            <a style="color: red" target="_blank" href="edit_text.php?path=' . base64_encode($file_path) . '">'
                                . htmlspecialchars($file_path_sort)
                            . '</a>';
                        echo '</div>';
                        echo '<div class="item-content">';
                    }

                    echo '<div class="item-content-item">
                        <b>' . $line_number . ':</b> '
                        . (
                            $case
                            ? str_replace(
                                htmlspecialchars($search),
                                '<span style="background-color: yellow">' . htmlspecialchars($search) . '</span>',
                                htmlspecialchars($line)
                            )
                            : preg_replace(
                                '#(' . preg_quote(htmlspecialchars($search)) . ')#i',
                                '<span style="background-color: yellow">${1}</span>',
                                htmlspecialchars($line)
                            )
                        )
                    . '</div>';
                } // end tìm thấy
                
                if ($fileObj->eof() && $display) {
                    if ($replaceCheck) {
                        $content = file_get_contents($fileObj->getRealPath());
                        $newContent = str_replace($search, $replace, $content);
                        file_put_contents($fileObj->getRealPath(), $newContent);

                        echo '<span style="color: blue">Đã thay thế!!!</span>';
                    }
                    // phải dời ra ngoài vì để ở trong
                    // sẽ bị đóng trước khi đọc hết
                    echo '</div>'; // item-content
                    echo '</div>'; // item
                }
            } // end read line
        } // end loop all file

        echo '</div>';

        echo '<div class="list">
            Tổng: <b>' . $files_search_count . '</b> mục.
        </div>';
    } // end check error
} // end submit

show_back();

require 'footer.php';
