<?php

use nightmare\fs;

defined('ACCESS') or exit;

$site_title = 'Xem tập tin nén';

$file = new SplFileInfo($curr_path);
$dir = dirname($file->getPathname());
$name = $file->getFilename();
$format = $file->getExtension();

if (!in_array($format, array('zip', 'jar'))) {
    require SITE_HEADER;

    echo '<div class="title">' . $site_title . '</div>
    <div class="list"><span>Tập tin không phải zip</span></div>
    <div class="title">Chức năng</div>';
    
    show_back();
} else {
    $site_title .= ':' . $name;

    require SITE_HEADER;

    $curr_path = isset($_GET['path_zip']) && !empty($_GET['path_zip']) ? process_path_zip($_GET['path_zip']) : null;
    $dir = process_directory($dir);
    $format = file_get_ext($name);
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
        echo '<div class="title">' . $site_title . '</div>
        <div class="list">
            <span>' . print_path($dir . '/' . $name) . '</span><hr/>
            <span>Tập tin nén bị lỗi không mở được</span>
        </div>';
    } else {
        $base = $curr_path == null || empty($curr_path) ? null : $curr_path . '/';

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

        if ($curr_path != null && strpos((string) $curr_path, '/') !== false) {
            $array = explode('/', (string) preg_replace('|^/(.*?)$|', '\1', (string) $curr_path));
            $html = '/<a href="' . action_link('file', ['act' => 'zip_view', 'path' => $file->getPathname()] + get_page_list_params()) . '">' . $root . '</a>';
            $item = null;
            $url = null;

            foreach ($array AS $key => $entry) {
                if ($key === 0) {
                    $separator = preg_match('|^\/(.*?)$|', (string) $curr_path) ? '/' : null;
                    $item = $separator . $entry;
                } else {
                    $item = '/' . $entry;
                }

                if ($key < count($array) - 1)
                    $html .= '/<a href="' . action_link('file', ['act' => 'zip_view', 'path' => $file->getPathname(), 'path_zip' => $url . $item] + get_page_list_params()) . '">';
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
            if ($curr_path == null)
                $html = '/' . $root;
            else
                $html = '/<a href="' . action_link('file', ['act' => 'zip_view', 'path' => $file->getPathname()] + get_page_list_params()) . '">' . $root . '</a>/' . $curr_path;
        }

        echo '<div class="title">' . $html . '</div>';
        echo '<ul class="list_file">';
        echo '<li class="normal">
            <span>' . print_path($dir . '/' . $name) . '</span>
        </li>';

        if ($curr_path != null) {
            $back = strrchr((string) $curr_path, '/');

            if ($back !== false)
                $back = action_link('file', ['act' => 'zip_view', 'path' => $dir . '/' . $name, 'path_zip' => substr((string) $curr_path, 0, strlen((string) $curr_path) - strlen($back))] + get_page_list_params());
            else
                $back = action_link('file', ['act' => 'zip_view', 'path' => $dir . '/' . $name] + get_page_list_params());

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
                $path_encode = rawurlencode((string) $value['path']);

                if ($value['folder']) {
                    echo '<li class="folder">
                        <div>
                            <img src="icon/folder.png"/>
                            <a href="' . action_link('file', ['act' => 'zip_view', 'path' => $dir . '/' . $name, 'path_zip' => $value['path']] + get_page_list_params()) . '">' . $value['name'] . '</a>
                        </div>
                    </li>';
                } else {
                    echo '<li class="file">
                        <p>
                            ' . get_file_icon_display($value['path']) . '
                            <span>' . $value['name'] . '</span>
                        </p>
                        <p>
                            <span class="size">' . fs::readable_size($value['size']) . '</span>
                        </p>
                    </li>';
                }
            }
        }

        echo '</ul>';
    }

    file_display_actions($file);
}

require SITE_FOOTER;
