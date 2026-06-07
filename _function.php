<?php

use nightmare\http\request;
use nightmare\config;
use nightmare\fs;
use nightmare\http\curl;
use nightmare\http\http;
use nightmare\zip;

defined('ACCESS') or exit;



// ========
//
//
function is_app_file($dir)
{
    return stripos((string) $dir, APP_PATH) === 0;
}



// ========
// base64url
//
function base64url_encode(string $data): string {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode(string $data): string {
    return base64_decode(strtr($data, '-_', '+/'));
}



// ========
// auth
//
function get_login_fail()
{
    $last_login = (int) config()->get(LOGIN_LOCK_KEY . '_time');
    $time_difference = time() - $last_login;

    // reset 30 phut
    if ($time_difference >= LOGIN_WAIT) {
        reset_fail_login();
    }

    return (int) config()->get(LOGIN_LOCK_KEY);
}

function increase_login_fail()
{
    config()->set(LOGIN_LOCK_KEY, get_login_fail() + 1);
    config()->set(LOGIN_LOCK_KEY . '_time', time());
}

function reset_fail_login()
{
    config()->set(LOGIN_LOCK_KEY, 0);
    config()->set(LOGIN_LOCK_KEY . '_time', 0);
}

function can_login()
{
    return get_login_fail() < LOGIN_MAX;
}



// ========
// config
//
function config()
{
    static $instance = null;

    if ($instance === null) {
        $instance = new fm_config(APP_CONFIG_FILE);
    }

    return $instance;
}

function response(...$args)
{
    return http::response(...$args);
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

function action_link(string $name, array $params = []): string
{
    $link = $name . '.php';

    if (empty($params)) {
        return $link;
    }

    return $link . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);
}

function get_page_list_params(): array
{
    global $pages;

    if (!isset($pages['current']) || $pages['current'] <= 1) {
        return [];
    }

    return ['page_list' => $pages['current']];
}

function app_in_web_root()
{
    $sapi = php_sapi_name();

    if ($sapi === 'cli' || $sapi === 'cli-server') {
        return false;
    }

    if (empty($_SERVER['DOCUMENT_ROOT'])) {
        return false;
    }

    $document_root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
    $current_dir = rtrim(APP_PATH, '/');

    return $current_dir === $document_root;
}

function auth_encode_pwd($pass)
{
    return md5($pass);
}


function get_file_ext($name)
{
    return strrchr((string) $name, '.') !== false
        ? strtolower(str_replace('.', '', strrchr((string) $name, '.')))
        : '';
}

function is_format_text($name)
{
    $format = get_file_ext($name);

    if (in_array($format, COMMON_FILE_FORMAT['text']) || in_array($format, COMMON_FILE_FORMAT['other'])) {
        return true;
    }

    $basename = strtolower(
        strpos($name, '.') !== false
            ? substr($name, 0, strpos($name, '.'))
            : $name
    );

    return in_array($basename, COMMON_FILE_FORMAT['source']);
}

function is_format_unknown($name)
{
    $format = get_file_ext($name);

    if (empty($format)) {
        return true;
    }

    foreach (COMMON_FILE_FORMAT as $array) {
        if (in_array($format, $array)) {
            return false;
        }
    }

    return true;
}

function str_replace_first($needle, $replace, $haystack)
{
    $pos = strpos((string) $haystack, (string) $needle);

    if ($pos !== false) {
        return substr_replace($haystack, $replace, $pos, strlen((string) $needle));
    }

    return $haystack;
}

function is_url($url)
{
    return filter_var($url, FILTER_VALIDATE_URL);
}

/**
 * @return string|false
 */
function create_tmp_file(string $name, bool $random = true)
{
    $prefix = APP_NAME . '_' . $name;

    if ($random) {
        return tempnam(sys_get_temp_dir(), $prefix . '_');
    }

    return sys_get_temp_dir() . '/' . $prefix;
}


function process_directory($var, $seSlash = false)
{
    if (empty($var)) {
        return '';
    }

    $var = str_replace('\\', '/', $var);
    $var = preg_replace('#/\./#', '//', $var);
    $var = preg_replace('#/\.\./#', '//', $var);
    $var = preg_replace('#/\.{1,2}$#', '//', $var);
    $var = preg_replace('|/{2,}|', '/', $var);
    $var = preg_replace('|(.+?)/$|', '$1', $var);

    // thêm / vào đầu và cuối
    if ($seSlash) {
        $var = trim($var, '/');
        $var = '/' . $var . '/';
    }

    return $var;
}

function process_path_zip($var)
{
    if (empty($var)) {
        $var = '';
    }

    $var = str_replace('\\', '/', $var);
    $var = preg_replace('#/\./#', '//', $var);
    $var = preg_replace('#/\.\./#', '//', $var);
    $var = preg_replace('#/\.{1,2}$#', '//', $var);
    $var = preg_replace('|/{2,}|', '/', $var);
    $var = preg_replace('|/?(.+?)/?$|', '$1', $var);

    return $var;
}

function process_name($var)
{
    $var = str_replace('/', '', $var);
    $var = str_replace('\\', '', $var);

    return $var;
}

function is_name_error($var)
{
    return strpos((string) $var, '\\') !== false || strpos((string) $var, '/') !== false;
}

function remove_dir($path)
{
    $handler = scandir($path);

    if ($handler !== false) {
        foreach ($handler as $entry) {
            if ($entry != '.' && $entry != '..') {
                $pa = $path . '/' . $entry;

                if (is_dir($pa)) {
                    if (!remove_dir($pa)) {
                        return false;
                    }
                } else {
                    if (!unlink($pa)) {
                        return false;
                    }
                }
            }
        }

        return is_dir($path) ? rmdir($path) : unlink($path);
    }

    return false;
}

function multi_remove($entrys, $dir)
{
    foreach ($entrys as $e) {
        if (!fs::remove($dir . '/' . $e)) {
            return false;
        }
    }
    return true;
}

function copydir($old, $new, $isParent = true)
{
    $handler = @scandir($old);

    if ($handler !== false) {
        if ($isParent && $old != '/') {
            $arr = explode('/', (string) $old);
            $end = $new = $new . '/' . end($arr);

            if (@is_file($end) || (!@is_dir($end) && !@mkdir($end))) {
                return false;
            }
        } elseif (!$isParent && !@is_dir($new) && !@mkdir($new)) {
            return false;
        }

        foreach ($handler as $entry) {
            if ($entry != '.' && $entry != '..') {
                $paOld = $old . '/' . $entry;
                $paNew = $new . '/' . $entry;

                if (@is_file($paOld)) {
                    if (!@copy($paOld, $paNew)) {
                        return false;
                    }
                } elseif (@is_dir($paOld)) {
                    if (!copydir($paOld, $paNew, false)) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }

        return true;
    }

    return false;
}

function movedir($old, $new, $isParent = true)
{
    $handler = @scandir($old);

    if ($handler !== false) {
        if ($isParent && $old != '/') {
            $s   = explode('/', (string) $old);
            $end = $new = $new . '/' . end($s);

            if (@is_file($end) || (!@is_dir($end) && !@mkdir($end))) {
                return false;
            }
        } elseif (!$isParent && !@is_dir($new) && !@mkdir($new)) {
            return false;
        }

        foreach ($handler as $entry) {
            if ($entry != '.' && $entry != '..') {
                $paOld = $old . '/' . $entry;
                $paNew = $new . '/' . $entry;

                if (@is_file($paOld)) {
                    if (!@rename($paOld, $paNew)) {
                        return false;
                    }
                } elseif (@is_dir($paOld)) {
                    if (!movedir($paOld, $paNew, false)) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }

        return @rmdir($old);
    }

    return false;
}

function moves($entrys, $dir, $path)
{
    foreach ($entrys as $e) {
        $pa = $dir . '/' . $e;

        if (@is_file($pa)) {
            if (!@rename($pa, $path . '/' . $e)) {
                return false;
            }
        } elseif (@is_dir($pa)) {
            if (!movedir($pa, $path)) {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}


function merge_folder($source, $destination, $overwrite = true)
{
    if (!is_dir($source)) {
        return false; // Source is not a directory
    }

    if (!file_exists($destination)) {
        mkdir($destination);
    }

    $dir = opendir($source);

    while ($file = readdir($dir)) {
        if ($file !== '.' && $file !== '..') {
            $src_file = $source . '/' . $file;
            $dst_file = $destination . '/' . $file;

            if (is_dir($src_file)) {
                merge_folder($src_file, $dst_file);
            } else {
                copy($src_file, $dst_file); // Overwrite existing files
            }
        }
    }

    closedir($dir);

    return true;
}

if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle)
    {
        $length = strlen((string) $needle);
        if ($length == 0) {
            return true;
        }
        return (substr((string) $haystack, -$length) === $needle);
    }
}

// chi dung de doc tat ca file
function read_full_dir($path, $excludes = [])
{
    $directory = new RecursiveDirectoryIterator(
        $path,
        FilesystemIterator::UNIX_PATHS
        | FilesystemIterator::SKIP_DOTS
    );

    $filter = new RecursiveCallbackFilterIterator($directory, function ($current, $key, $iterator) use ($path, $excludes) {
        $relativePath = str_replace_first($path, '', $current->getPathname());

        foreach ($excludes as $exclude) {
            if (empty($exclude)) {
                continue;
            }
            //var_dump($relativePath);
            //var_dump($exclude);

            $exclude = trim($exclude);
            $exclude = trim($exclude, '/');
            $relativePath = trim($relativePath, '/');

            if (str_ends_with($relativePath, $exclude)) {
                return false;
            }
        }

        return true;
    });

    return new RecursiveIteratorIterator(
        $filter,
        RecursiveIteratorIterator::SELF_FIRST
    );
}

function file_import($path, $url, $timeout = 0) {
    $fp = fopen($path, 'wb');

    if ($fp === false) {
        return false;
    }

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_FILE => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_FAILONERROR => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);

    $ok = curl_exec($ch);
    //curl_close($ch);
    fclose($fp);

    return $ok;
}

function app_reinstall(): bool
{
    $file = create_tmp_file('reinstall');

    if ($file === false) {
        return false;
    }

    try {
        if (!file_import($file, REMOTE_FILE_URL)) {
            return false;
        }

        $zip = new zip;

        if ($zip->open($file) !== true) {
            return false;
        }

        try {
            return $zip->extractTo(APP_PATH) === true;
        } finally {
            $zip->close();
        }
    } finally {
        if (file_exists($file)) {
            @unlink($file);
        }
    }
}

function page($current, $total, $url)
{
    $html = '<div class="page">';
    $center = PAGE_NUMBER - 2;
    $link = [];
    $link[PAGE_URL_DEFAULT] = $url[PAGE_URL_DEFAULT] ?? null;
    $link[PAGE_URL_START] = $url[PAGE_URL_START] ?? null;
    $link[PAGE_URL_END] = $url[PAGE_URL_END] ?? null;

    if ($total <= PAGE_NUMBER) {
        for ($i = 1; $i <= $total; ++$i) {
            if ($current == $i) {
                $html .= '<strong class="current">' . $i . '</strong>';
            } else {
                if ($i == 1) {
                    $html .= '<a href="' . $link[PAGE_URL_DEFAULT] . '" class="other">' . $i . '</a>';
                } else {
                    $html .= '<a href="' . $link[PAGE_URL_START] . $i . $link[PAGE_URL_END] . '" class="other">' . $i . '</a>';
                }
            }
        }
    } else {
        if ($current == 1) {
            $html .= '<strong class="current">1</strong>';
        } else {
            $html .= '<a href="' . $link[PAGE_URL_DEFAULT] . '" class="other">1</a>';
        }

        if ($current > $center) {
            $i = $current - $center < 1 ? 1 : $current - $center;

            if ($i == 1) {
                $html .= '<a href="' . $link[PAGE_URL_DEFAULT] . '" class="text">...</a>';
            } else {
                $html .= '<a href="' . $link[PAGE_URL_START] . $i . $link[PAGE_URL_END] . '" class="text">...</a>';
            }
        }

        $offset = [];

        {
            if ($current <= $center) {
                $offset['start'] = 2;
            } else {
                $offset['start'] = $current - ($current > $total - $center ? $current - ($total - $center) : floor($center >> 1));
            }

            if ($current >= $total - $center + 1) {
                $offset['end'] = $total - 1;
            } else {
                $offset['end'] = $current + ($current <= $center ? ($center + 1) - $current : floor($center >> 1));
            }
        }

        for ($i = $offset['start']; $i <= $offset['end']; ++$i) {
            if ($current == $i) {
                $html .= '<strong class="current">' . $i . '</strong>';
            } else {
                $html .= '<a href="' . $link[PAGE_URL_START] . $i . $link[PAGE_URL_END] . '" class="other">' . $i . '</a>';
            }
        }

        if ($current < $total - $center + 1) {
            $html .= '<a href="' . $link[PAGE_URL_START] . ($current + $center > $total ? $total : $current + $center) . $link[PAGE_URL_END] . '" class="text">...</a>';
        }

        if ($current == $total) {
            $html .= '<strong class="current">' . $total . '</strong>';
        } else {
            $html .= '<a href="' . $link[PAGE_URL_START] . $total . $link[PAGE_URL_END] . '" class="other">' . $total . '</a>';
        }
    }

    $html .= '</div>';

    return $html;
}

function file_get_chmod($path)
{
    $perms = @fileperms($path);

    if ($perms !== false) {
        $perms = decoct($perms);
        $perms = substr($perms, strlen($perms) == 5 ? 2 : 3, 3);
    } else {
        $perms = 0;
    }

    return $perms;
}

function count_string_array($array, $search, $isLowerCase = false)
{
    $count = 0;

    if ($array != null && is_array($array)) {
        foreach ($array as $entry) {
            if ($isLowerCase) {
                $entry = strtolower((string) $entry);
            }

            if ($entry == $search) {
                ++$count;
            }
        }
    }

    return $count;
}

function is_in_array($array, $search, $isLowerCase)
{
    if ($array == null || !is_array($array)) {
        return false;
    }

    foreach ($array as $entry) {
        if ($isLowerCase) {
            $entry = strtolower((string) $entry);
        }

        if ($entry == $search) {
            return true;
        }
    }

    return false;
}

function print_path(string $path, bool $isHrefEnd = false)
{
    $html = '';

    if ($path && $path != '/' && strpos($path, '/') !== false) {
        $array = explode('/', (string) preg_replace('|^/(.*?)$|', '\1', $path));
        $item  = null;
        $url   = null;

        foreach ($array as $key => $entry) {
            if ($key === 0) {
                $seperator = preg_match('|^\/(.*?)$|', $path) ? '/' : null;
                $item      = $seperator . $entry;
            } else {
                $item = '/' . $entry;
            }

            if ($key < count($array) - 1 || ($key == count($array) - 1 && $isHrefEnd)) {
                $html .= '<span class="path_seperator">/</span><a href="' . action_link('index', ['path' => $url . $item]) . '">';
            } else {
                $html .= '<span class="path_seperator">/</span>';
            }

            $url  .= $item;
            $html .= '<span class="path_entry">' . $entry . '</span>';

            if ($key < count($array) - 1 || ($key == count($array) - 1 && $isHrefEnd)) {
                $html .= '</a>';
            }
        }
    }

    return $html;
}

function is_function_exec_enable()
{
    return function_exists('exec')
        && is_function_disable('exec') == false;
}
function is_function_disable($func)
{
    $list = @ini_get('disable_functions');

    if (empty($list) == false) {
        $func = strtolower(trim((string) $func));
        $list = explode(',', $list);

        foreach ($list as $e) {
            if (strtolower(trim($e)) == $func) {
                return true;
            }
        }
    }

    return false;
}
function run_command($command)
{
    $descriptorspec = [
        0 => ["pipe", "r"],  // stdin
        1 => ["pipe", "w"],  // stdout
        2 => ["pipe", "w"]   // stderr
    ];

    // Mở tiến trình
    $process = proc_open($command, $descriptorspec, $pipes);

    if (is_resource($process)) {
        // Đọc đầu ra từ stdout
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]); // Đóng luồng stdout

        // Đọc lỗi từ stderr
        $error = stream_get_contents($pipes[2]);
        fclose($pipes[2]); // Đóng luồng stderr

        // Đóng tiến trình
        $return_value = proc_close($process);

        // Hiển thị kết quả
        return [
            'out' => $output,
            'err' => $error,
            'code' => $return_value
        ];
    } else {
        return false;
    }
}

function asset($asset)
{
    return $asset . '?' .  filemtime($asset);
}

function sort_natural(&$items)
{
    usort($items, function ($a, $b) {
        $a_is_letter = ctype_alpha($a[0]);
        $b_is_letter = ctype_alpha($b[0]);
        return $a_is_letter === $b_is_letter
            ? strnatcmp($a, $b)
            : ($a_is_letter ? 1 : -1);
    });
}

function get_file_icon(string $path): string
{
    if (is_dir($path)) {
        return 'icon/folder.png';
    }

    $name = basename($path);
    $type = get_file_ext($name);
    $icon = 'unknown';

    if (in_array($type, COMMON_FILE_FORMAT['other'])) {
        $icon = $type;
    } elseif (in_array($type, COMMON_FILE_FORMAT['text'])) {
        $icon = $type;
    } elseif (in_array($type, COMMON_FILE_FORMAT['archive'])) {
        $icon = $type;
    } elseif (in_array($type, COMMON_FILE_FORMAT['audio'])) {
        $icon = $type;
    } elseif (in_array($type, COMMON_FILE_FORMAT['font'])) {
        $icon = $type;
    } elseif (in_array($type, COMMON_FILE_FORMAT['binary'])) {
        $icon = $type;
    } elseif (in_array($type, COMMON_FILE_FORMAT['document'])) {
        $icon = $type;
    } elseif (in_array($type, COMMON_FILE_FORMAT['image'])) {
        $icon = 'image';
    } elseif (in_array(
        strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name),
        COMMON_FILE_FORMAT['source']
    )) {
        $icon = strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name);
    }

    return 'icon/mime/' . $icon . '.png';
}

function get_file_icon_display(string $path): string
{
    return '<img src="' . get_file_icon($path) . '"/>';
}

function show_back()
{
    echo '<a href="javascript:history.back()">
      <img src="icon/back.png"> 
      <strong class="back">Trở lại</strong>
    </a>';
}

function can_format_code($type)
{
    return in_array($type, [
        'php',
        'html',
        'js',
        'ts',
        'css',
        'scss',
        'json',
        'yaml'
    ]);
}


function check_path($path, $type = '')
{
    extract($GLOBALS);

    if ($type == 'file') {
        $name = 'Tập tin';

        if (@is_file($path)) {
            return;
        }
    } else if ($type == 'folder') {
        $name = 'Thư mục';

        if (@is_dir($path)) {
            return;
        }
    } else {
        $name = 'Đường dẫn';

        if (@file_exists($path)) {
            return;
        }
    }

    $site_title = 'Lỗi - ' . $path;

    require SITE_HEADER;

    echo '<div class="title">' . print_path($path, true) . '</div>';
    echo '<div class="notice_failure">' . $name . ' <b><i>bị hệ thống chặn</i></b> hoặc <b><i>không tồn tại</i></b>!</div>';
    echo '<br>';

    show_back();

    require SITE_FOOTER;
    exit;
}



function form_err($err)
{
    if (empty($err)) {
        return '';
    }

    return '<div class="notice_failure">' . is_array($err) ? $err[0] : $err . '</div>';
}







function get_curr_path()
{
    $path = (string) request::get('path');

    if (!empty($path) && $path[0] !== '/') {
        $path = base64url_decode($path);
    }

    return $path;
}

function zip_dir($path, $file, $isDelete = false)
{
    if (@is_file($file)) {
        @unlink($file);
    }

    $zip = new zip();

    if ($zip->open($file, ZipArchive::CREATE) === true) {
        $path = realpath($path);
        $files = read_full_dir($path);

        foreach ($files as $name => $file) {
            $filePath = $file->getRealPath();
            $zip->add($filePath, $path . DIRECTORY_SEPARATOR);
        }

        $zip->close();

        if ($isDelete) {
            remove_dir($path);
        }

        return true;
    }

    return false;
}

function print_actions($filename)
{
    global $pages;

    $file = new SplFileInfo($filename);
    $path = $file->getPathname();
    $name = $file->getFilename();
    $ext = $file->getExtension();
    $dir = dirname($path);

    echo '<div class="title">Chức năng</div>';
    echo '<ul class="list">';

    if ($file->isFile()) {
        if (in_array($ext, COMMON_FILE_FORMAT['zip'])) {
            echo '<li><img src="icon/unzip.png"/> <a href="' . action_link('file', ['act' => 'zip_view', 'path' => $path] + get_page_list_params()) . '">Xem</a></li>
              <li><img src="icon/unzip.png"/> <a href="' . action_link('file', ['act' => 'unzip', 'path' => $path] + get_page_list_params()) . '">Giải nén</a></li>';
        } elseif (is_format_text($name) || is_format_unknown($name)) {
            echo '<li><img src="icon/edit.png"/> <a href="' . action_link('file', ['act' => 'edit_text', 'path' => $path]) . '">Sửa văn bản</a></li>
              <li><img src="icon/edit_text_line.png"/> <a href="' . action_link('file', ['act' => 'code_edit', 'path' => $path]) . '">Sửa code</a></li>
               <li><img src="icon/edit_text_line.png"/> <a href="' . action_link('file', ['act' => 'edit_text_line', 'path' => $path] + get_page_list_params()) . '">Sửa theo dòng</a></li>
              <li><img src="icon/columns.png"/> <a href="' . action_link('file', ['act' => 'code_view', 'path' => $path]) . '">Xem code</a></li>';
        }
        echo '<li><img src="icon/download.png"/> <a href="' . action_link('file', ['act' => 'download', 'path' => $path]) . '">Tải về</a></li>';
    } else {
        echo '<li><img src="icon/zip.png"/> <a href="' . action_link('folder_zip', ['dir' => $dir, 'name' => $name] + get_page_list_params()) . '">Nén zip</a></li>';
    }

    echo '<li><img src="icon/rename.png"/> <a href="' . action_link('file', ['act' => 'rename', 'path' => $path] + get_page_list_params()) . '">Đổi tên</a></li>';
    echo '<li><img src="icon/copy.png"/> <a href="' . action_link('file', ['act' => 'copy', 'path' => $path] + get_page_list_params()) . '">Sao chép</a></li>';
    echo '<li><img src="icon/move.png"/> <a href="' . action_link('file', ['act' => 'move', 'path' => $path] + get_page_list_params()) . '">Di chuyển</a></li>';
    echo '<li><img src="icon/access.png"/> <a href="' . action_link('file', ['act' => 'chmod', 'path' => $path] + get_page_list_params()) . '">Chmod</a></li>';
    echo '<li><img src="icon/delete.png"/> <a href="' . action_link('file', ['act' => 'delete', 'path' => $path] + get_page_list_params()) . '">Xóa</a></li>';

    echo '<li><img src="icon/info.png"/> <a href="' . action_link('file', ['act' => 'info', 'path' => $path] + get_page_list_params()) . '">Thông tin</a></li>';
    echo '<li><img src="icon/list.png"/> <a href="' . action_link('index', ['path' => $dir] + get_page_list_params()) . '">Danh sách</a></li>';
    echo '</ul>';

    show_back();
}

function t_file_type($filename)
{
    if (is_file($filename)) {
        return 'tập tin';
    }
    if (is_dir($filename)) {
        return 'thư mục';
    }
    return '(unknown)';
}

function file_get_display_link($file)
{
    global $pages;

    $path = $file->getPathname();
    $file_dir = $file->isDir() ? $file->getPathname() : dirname($file->getPathname());
    $name = $file->getFilename();
    $is_edit = false;

    $file_icon = get_file_icon_display($path);

    if ($file->isFile()) {
        if (in_array($file->getExtension(), COMMON_FILE_FORMAT['text'])) {
            $is_edit = true;
        } elseif (in_array(
            strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name),
            COMMON_FILE_FORMAT['source']
        )) {
            $is_edit = true;
        } elseif (is_format_unknown($name)) {
            $is_edit = true;
        }

        if (strtolower($file->getFilename()) === 'error_log' || $is_edit) {
            $file_link = action_link('file', ['act' => 'edit_text', 'path' => $file->getPathname()]);
        } elseif (in_array($file->getExtension(), COMMON_FILE_FORMAT['zip'])) {
            $file_link = action_link('file', ['act' => 'unzip', 'path' => $file->getPathname()] + get_page_list_params());
        } else {
            $file_link = action_link('file', ['act' => 'rename', 'path' => $path] + get_page_list_params());
        }
    } else {
        $file_link = action_link('file', ['act' => 'rename', 'path' => $path] + get_page_list_params());
    }

    $file_icon = sprintf('<a href="%s">%s</a>', $file_link, $file_icon);

    if (is_app_file($path)) {
        $name_display = '<span style="color: red !important">' . $name . '</span>';
    } else {
        $name_display = $name;
    }

    if ($file->isLink()) {
        $name_display = '<span style="color:darkcyan">' . $name_display . '</span>';
    }

    return sprintf(
        '%s <a href="%s">%s</a>',
        $file_icon,
        $file->isDir() ? action_link('index', ['path' => $file_dir]) : action_link('file', ['act' => 'info', 'path' => $path]),
        $name_display
    );
}










class fm_bookmark
{
    public static function get(): array
    {
        return config()->get('bookmarks', []);
    }

    private static function save(array $data): void
    {
        config()->set('bookmarks', $data);
    }

    public static function add($path): void
    {
        $bookmarks = self::get();
        $bookmarks[] = $path;
        $bookmarks = array_unique($bookmarks);

        self::save($bookmarks);
    }

    public static function delete($path): void
    {
        $bookmarks = self::get();
        $bookmarks = array_diff($bookmarks, [$path]);

        self::save($bookmarks);
    }
}

class fm_file_edit_recent
{
    public static function get(): array
    {
        return config()->get('edit_recent', []);
    }

    private static function save(array $data): void
    {
        config()->set('edit_recent', $data);
    }

    public static function add($path): void
    {
        $old = self::get();
        $old = array_values(array_diff($old, [$path]));
        array_unshift($old, $path);
        $old = array_slice($old, 0, 20);
        self::save($old);
    }

    public static function clear(): void
    {
        config()->set('edit_recent', []);
    }
}

class fm_config
{
    private array $configs = [];
    private string $config_file;
    private string $prefix;

    public function __construct(string $config_file)
    {
        $this->config_file = $config_file;
        $this->prefix = "<?php defined('ACCESS') or exit; ?>\n";
        $this->init();
    }

    public function init(): void
    {
        $content = (string) @file_get_contents($this->config_file);

        if (strncmp($content, $this->prefix, strlen($this->prefix)) !== 0) {
            $this->configs = [];
            $this->save();
            return;
        }

        $json = substr($content, strlen($this->prefix));
        $data = json_decode($json, true);
        $this->configs = is_array($data) ? $data : [];
    }

    public function save(): void
    {
        $content = $this->prefix . json_encode($this->configs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        file_put_contents($this->config_file, $content);
    }

    public function get(string $key, $default = null)
    {
        return $this->configs[$key] ?? $default;
    }

    public function set(string $key, $value): void
    {
        $this->configs[$key] = $value;
        $this->save();
    }

    public function unset(string $key): void
    {
        unset($this->configs[$key]);
        $this->save();
    }

}


