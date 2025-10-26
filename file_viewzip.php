<?php
namespace app;

use SplFileInfo;
use ZipArchive;

define('ACCESS', true);

require '_init.php';

$title = 'Xem tập tin nén';

check_path($path, 'file');

$file = new SplFileInfo($path);
$dir = dirname($file->getPathname());
$name = $file->getFilename();
$format = $file->getExtension();

if (!in_array($format, array('zip', 'jar'))) {
    require_once '_header.php';

    echo '<div class="title">' . $title . '</div>
    <div class="list"><span>Tập tin không phải zip</span></div>
    <div class="title">Chức năng</div>';
    
    show_back();
} else {
    $title .= ':' . $name;

    require '_header.php';

    $path = isset($_GET['path_zip']) && !empty($_GET['path_zip']) ? process_path_zip($_GET['path_zip']) : null;
    $dir = process_directory($dir);
    $format = get_format($name);
    $zip = new ZipArchive;
    $zip->open($dir . '/' . $name);
    $lists = [];
    $arrays = array('folders' => [], 'files' => []);
    for($i = 0; $i < $zip->numFiles; $i++)
    {   
        $stat = $zip->statIndex($i);
        if ($stat['size']) {
            $folder = false;
        } else {
            $folder = true;
        }
        $lists[$i] = [
            "filename" => $zip->getNameIndex($i),
            "stored_filename" => $zip->getNameIndex($i),
            "size" => $stat['size'],
            "compressed_size" => $stat['comp_size'],
            "mtime" => $stat['mtime'],
            "comment" => "",
            "folder" => $folder,
            "index" => $stat['index'],
            "status" => "ok",
            "crc" => $stat['crc']
        ];
    }
    
    if (!$lists) {
        echo '<div class="title">' . $title . '</div>
        <div class="list">
            <span>' . print_path($dir . '/' . $name) . '</span><hr/>
            <span>Tập tin nén bị lỗi không mở được</span>
        </div>';
    } else {
        $base = $path == null || empty($path) ? null : $path . '/';

        foreach ($lists AS $entry) {
            $filename = $entry['filename'];

            if (strpos((string) $filename, '/') === false && $base == null) {
                $arrays['files'][$filename] = array('path' => $filename, 'name' => $filename, 'folder' => false, 'size' => $entry['size']);
            } else if (preg_match('#(' . $base . '(.+?))(/|$)+#', (string) $filename, $matches)) {
                if ($matches[3] == '/' && !isset($arrays['folders'][$matches[2]]))
                    $arrays['folders'][$matches[2]] = array('path' => $matches[1], 'name' => $matches[2], 'folder' => true);
                else if ($matches[3] != '/' && !$entry['folder'])
                    $arrays['files'][$matches[2]] = array('path' => $matches[1], 'name' => $matches[2], 'folder' => false, 'size' => $entry['size']);
            }
        }

        $sorts = [];

        if (count($arrays['folders']) > 0) {
            ksort($arrays['folders']);

            foreach ($arrays['folders'] AS $entry)
                $sorts[] = $entry;
        }

        if (count($arrays['files']) > 0) {
            ksort($arrays['files']);

            foreach ($arrays['files'] AS $entry)
                $sorts[] = $entry;
        }

        array_splice($arrays, 0, count($arrays));

        $arrays = $sorts;
        $count = count($arrays);
        $root = 'root';
        $html = null;

        array_splice($sorts, 0, count($sorts));
        unset($sorts);

        if ($path != null && strpos((string) $path, '/') !== false) {
            $array = explode('/', (string) preg_replace('|^/(.*?)$|', '\1', (string) $path));
            $html = '/<a href="file_viewzip.php?path=' . $file->getPathname() . $pages['paramater_1'] . '">' . $root . '</a>';
            $item = null;
            $url = null;

            foreach ($array AS $key => $entry) {
                if ($key === 0) {
                    $seperator = preg_match('|^\/(.*?)$|', (string) $path) ? '/' : null;
                    $item = $seperator . $entry;
                } else {
                    $item = '/' . $entry;
                }

                if ($key < count($array) - 1)
                    $html .= '/<a href="file_viewzip.php?path=' . $file->getPathname() . '&path_zip=' . rawurlencode($url . $item) . $pages['paramater_1'] . '">';
                else
                    $html .= '/';

                $url .= $item;

                if (strlen($entry) <= 8)
                    $html .= $entry;
                else
                    $html .= substr($entry, 0, 8) . '...';

                if ($key < count($array) - 1)
                    $html .= '</a>';
            }
        } else {
            if ($path == null)
                $html = '/' . $root;
            else
                $html = '/<a href="file_viewzip.php?path=' . $file->getPathname() . $pages['paramater_1'] . '">' . $root . '</a>/' . $path;
        }

        echo '<div class="title">' . $html . '</div>';
        echo '<ul class="list_file">';
        echo '<li class="normal">
            <span>' . print_path($dir . '/' . $name) . '</span>
        </li>';

        if ($path != null) {
            $back = strrchr((string) $path, '/');

            if ($back !== false)
                $back = 'file_viewzip.php?dir=' . $dirEncode . '&name=' . $name . '&path=' . rawurlencode(substr((string) $path, 0, strlen((string) $path) - strlen($back))) . $pages['paramater_1'];
            else
                $back = 'file_viewzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'];

            echo '<li class="normal">
                <img src="icon/back.png" style="margin-left: 5px; margin-right: 5px"/> 
                <a href="' . $back . '">
                    <strong class="back">...</strong>
                </a>
            </li>';
        }

        if ($count <= 0) {
            echo '<li class="normal"><img src="icon/empty.png"/> <span class="empty">Không có thư mục hoặc tập tin</span></li>';
        } else {
            foreach ($arrays AS $key => $value) {
                $pathEncode = rawurlencode((string) $value['path']);

                if ($value['folder']) {
                    echo '<li class="folder">
                        <div>
                            <img src="icon/folder.png" style="margin-left: 5px"/>
                            <a href="file_viewzip.php?dir=' . $dirEncode . '&name=' . $name . '&path=' . $pathEncode . $pages['paramater_1'] . '">' . $value['name'] . '</a>
                        </div>
                    </li>';
                } else {
                    $icon = 'unknown';
                    $type = get_format($value['name']);

                    if (in_array($type, $formats['other']))
                        $icon = $type;
                    else if (in_array($type, $formats['text']))
                        $icon = $type;
                    else if (in_array($type, $formats['archive']))
                        $icon = $type;
                    else if (in_array($type, $formats['audio']))
                        $icon = $type;
                    else if (in_array($type, $formats['font']))
                        $icon = $type;
                    else if (in_array($type, $formats['binary']))
                        $icon = $type;
                    else if (in_array($type, $formats['document']))
                        $icon = $type;
                    else if (in_array($type, $formats['image']))
                        $icon = 'image';
                    else if (in_array(strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name), $formats['source']))
                        $icon = strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name);

                    echo '<li class="file">
                        <p>
                            <img src="icon/mime/' . $icon . '.png" style="margin-left: 5px"/>
                            <span>' . $value['name'] . '</span>
                        </p>
                        <p>
                            <span class="size">' . size($value['size']) . '</span>
                        </p>
                    </li>';
                }
            }
        }

        echo '</ul>';
    }

    print_actions($file);
}

require '_footer.php';
