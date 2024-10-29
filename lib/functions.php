<?php

function isAppFile($dir)
{
    return stripos($dir, REALPATH) === 0;
}
function isAppDir($dir)
{
    return isAppFile($dir);
}
function isInstallAsRoot($dir)
{
    return isAppFile($dir);
}

function createConfig(
    $username = LOGIN_USERNAME_DEFAULT,
    $password = LOGIN_PASSWORD_DEFAULT,
    $pageList = PAGE_LIST_DEFAULT,
    $pageFileEdit = PAGE_FILE_EDIT_DEFAULT,
    $pageFileEditLine = PAGE_FILE_EDIT_LINE_DEFAULT,
    $pageDatabaseListRows = PAGE_DATABASE_LIST_ROWS_DEFAULT,
    $isEncodePassword = true
) {
    $content = "<?php if (!defined('ACCESS')) die('Not access'); else \$configs = array(";
    $content .= "'username' => '$username', ";
    $content .= "'password' => '" . ($isEncodePassword ? getPasswordEncode($password) : $password) . "', ";
    $content .= "'page_list' => '$pageList', ";
    $content .= "'page_file_edit' => '$pageFileEdit', ";
    $content .= "'page_file_edit_line' => '$pageFileEditLine',";
    $content .= "'page_database_list_rows' => '$pageDatabaseListRows',";
    $content .= '); ?>';

    if (is_file(PATH_CONFIG)) {
        unlink(PATH_CONFIG);
    }

    $put = file_put_contents(PATH_CONFIG, $content);

    if ($put) {
        return true;
    } else {
        $handler = @fopen(PATH_CONFIG, "w+");

        if ($handler) {
            if (@fwrite($handler, $content)) {
                @fclose($handler);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}


function createDatabaseConfig($host, $username, $password, $name, $auto)
{
    $content = "<?php if (!defined('ACCESS')) die('Not access'); else \$databases = array(";
    $content .= "'db_host' => '$host', ";
    $content .= "'db_username' => '$username', ";
    $content .= "'db_password' => '$password', ";
    $content .= "'db_name' => '$name', ";
    $content .= "'is_auto' => " . ($auto == true ? 'true' : 'false') . "";
    $content .= '); ?>';

    if (@is_file(REALPATH . '/' . PATH_DATABASE)) {
        @unlink(REALPATH . '/' . PATH_DATABASE);
    }

    $put = @file_put_contents(REALPATH . '/' . PATH_DATABASE, $content);

    if ($put) {
        return true;
    } else {
        $handler = @fopen(REALPATH . '/' . PATH_DATABASE, "w+");

        if ($handler) {
            if (@fwrite($handler, $content)) {
                @fclose($handler);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}

function isDatabaseVariable($array)
{
    return is_array($array) &&
                isset($array['db_host']) &&
                isset($array['db_username']) &&
                isset($array['db_password']) &&
                isset($array['db_name']) &&
                isset($array['is_auto']) &&
                empty($array['db_host']) == false && $array['db_host'] != null &&
                empty($array['db_username']) == false && $array['db_username'] != null;
}

function getNewVersion()
{
    if (!defined('alwaysCheckUpdate')) {
        if (cookie('fm_check_update')) {
            return false;
        } else {
            // 3 hour
            cookie(['fm_check_update' => 1], ['expires' => time() + 3600*3]);
        }
    }

    $remoteVersion = json_decode(@file_get_contents(REMOTE_VERSION_FILE), true);

    return is_array($remoteVersion) && isset($remoteVersion['message'])
        ? $remoteVersion
        : false;
}


function hasNewVersion()
{
    return REMOTE_FILE_CURRENT !== REMOTE_FILE_NEW;
}


function goURL($url)
{
    header('Location:' . $url);
    exit(0);
}


function getPasswordEncode($pass)
{
    return md5(md5(trim($pass)));
}


function getFormat($name)
{
    return strrchr($name, '.') !== false
        ? strtolower(str_replace('.', '', strrchr($name, '.')))
        : '';
}


function isFormatText($name)
{
    global $formats;

    $format = getFormat($name);

    if ($format == null) {
        return false;
    }

    return in_array($format, $formats['text']) || in_array($format, $formats['other']) || in_array(strtolower(strpos($name, '.') !== false ? substr($name, 0, strpos($name, '.')) : $name), $formats['source']);
}


function isFormatUnknown($name)
{
    global $formats;

    $format = getFormat($name);

    if ($format == null) {
        return true;
    }

    foreach ($formats as $array) {
        if (in_array($format, $array)) {
            return false;
        }
    }

    return true;
}

function str_replace_first($needle, $replace, $haystack)
{
    $pos = strpos($haystack, $needle);

    if ($pos !== false) {
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    }

    return $haystack;
}

function isURL($url)
{
    return filter_var($url, FILTER_VALIDATE_URL);
}


function processDirectory($var, $seSlash = false)
{
    if (empty($var)) {
        $var = '';
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


function processImport($url)
{
    if (!preg_match('|^http[s]?://(.+?)$|i', $url)) {
        $url = 'http://' . $url;
    }

    return $url;
}


function processPathZip($var)
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

function processName($var)
{
    $var = str_replace('/', '', $var);
    $var = str_replace('\\', '', $var);

    return $var;
}

function isNameError($var)
{
    return strpos($var, '\\') !== false || strpos($var, '/') !== false;
}

function rrmdir($path)
{
    $handler = scandir($path);

    if ($handler !== false) {
        foreach ($handler as $entry) {
            if ($entry != '.' && $entry != '..') {
                $pa = $path . '/' . $entry;

                if (is_dir($pa)) {
                    if (!rrmdir($pa)) {
                        return false;
                    }
                } else {
                    if (!unlink($pa)) {
                        return false;
                    }
                }
            }
        }

        return rmdir($path);
    }

    return false;
}

function removeDir($path)
{
    return rrmdir($path);
}

function rrms($entrys, $dir)
{
    foreach ($entrys as $e) {
        $pa = $dir . '/' . $e;

        if (@is_file($pa)) {
            if (!@unlink($pa)) {
                return false;
            }
        } elseif (@is_dir($pa)) {
            if (!rrmdir($pa)) {
                return false;
            }
        } else {
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
            $arr = explode('/', $old);
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

function copys($entrys, $dir, $path)
{
    foreach ($entrys as $e) {
        $pa = $dir . '/' . $e;

        if (isPathNotPermission(processDirectory($path . '/' . $e))) {
            /* Entry not permission */
        } elseif (@is_file($pa)) {
            if (!@copy($pa, $path . '/' . $e)) {
                return false;
            }
        } elseif (@is_dir($pa)) {
            if (!copydir($pa, $path)) {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}

function movedir($old, $new, $isParent = true)
{
    $handler = @scandir($old);

    if ($handler !== false) {
        if ($isParent && $old != '/') {
            $s   = explode('/', $old);
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


function mergeFolder($source, $destination, $overwrite = true)
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
                mergeFolder($src_file, $dst_file);
            } else {
                copy($src_file, $dst_file); // Overwrite existing files
            }
        }
    }

    closedir($dir);

    return true;
}

if (!function_exists('str_ends_with')) {
function str_ends_with($haystack, $needle) {
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }
    return (substr($haystack, -$length) === $needle);
}
}

// chi dung de doc tat ca file
function readFullDir($path, $excludes = []) {
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


function dirSize($path)
{
    $size = 0;

    $files = readFullDir($path);

    foreach ($files as $file) {
        if ($file->isFile()) {
            $size += $file->getSize();
        }
    }

    return $size;
}

function zips($dir, $entrys, $file, $isDelete = false)
{
    if (@is_file($file)) {
        @unlink($file);
    }

    $zip = new Zip();
    if ($zip->open($file, ZipArchive::CREATE) !== true) {
        return false;
    }
    foreach ($entrys as $entry) {
        $path = "$dir/$entry";
        $zip->add($path, $dir);
        
        if (is_dir($path)) {
            $files = readFullDir($path);
            
            foreach ($files as $value) {
                $zip->add($value->getPathname(), $dir);
            }
        }
    }
    $zip->close();

    if ($isDelete) {
        rrms($entrys, $dir);
    }

    return true;
}

function chmods($dir, $entrys, $folder, $file)
{
    $folder = intval($folder, 8);
    $file   = intval($file, 8);

    foreach ($entrys as $e) {
        $path = $dir . '/' . $e;

        if (@is_file($path)) {
            if (!@chmod($path, $file)) {
                return false;
            }
        } elseif (@is_dir($path)) {
            if (!@chmod($path, $folder)) {
                return false;
            }
        } else {
            return false;
        }
    }

    return true;
}

function size($size)
{
    $size = (int) $size;

    if ($size < 1024) {
        $size = $size . 'B';
    } elseif ($size < 1048576) {
        $size = round($size / 1024, 2) . 'KB';
    } elseif ($size < 1073741824) {
        $size = round($size / 1048576, 2) . 'MB';
    } else {
        $size = round($size / 1073741824, 2) . 'GB';
    }

    return $size;
}

function grab($url, $ref = '', $cookie = '', $user_agent = '', $header = '')
{
    $ch = curl_init();

    $headers[] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
    $headers[] = 'Accept-Language: en-us,en;q=0.5';
    $headers[] = 'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7';
    $headers[] = 'Keep-Alive: 300';
    $headers[] = 'Connection: Keep-Alive';
    $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';

    curl_setopt($ch, CURLOPT_URL, $url);

    if ($user_agent) {
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    } else {
        curl_setopt($ch, CURLOPT_USERAGENT, 'Nokia3110c/2.0 (04.91) Profile/MIDP-2.0 Configuration/CLDC-1.1');
    }

    if ($header) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
    } else {
        curl_setopt($ch, CURLOPT_HEADER, 0);
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($ref) {
        curl_setopt($ch, CURLOPT_REFERER, $ref);
    } else {
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com.vn/search?hl=vi&client=firefox-a&rls=org.mozilla:en-US:official&hs=hKS&q=video+clip&start=20&sa=N');
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if (strncmp($url, 'https', 6)) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    }

    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    }

    curl_setopt($ch, CURLOPT_TIMEOUT, 100);

    $html       = curl_exec($ch);
    $mess_error = curl_error($ch);

    curl_close($ch);

    return $html;
}

function import($url, $path)
{
    $binarys = file_get_contents($url);

    if (!file_put_contents($path, $binarys)) {
        //@unlink($path);
        return false;
    }

    return true;
}

function page($current, $total, $url)
{
    $html = '<div class="page">';
    $center = PAGE_NUMBER - 2;
    $link = array();
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

        $offset = array();

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

function getChmod($path)
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

function countStringArray($array, $search, $isLowerCase = false)
{
    $count = 0;

    if ($array != null && is_array($array)) {
        foreach ($array as $entry) {
            if ($isLowerCase) {
                $entry = strtolower($entry);
            }

            if ($entry == $search) {
                ++$count;
            }
        }
    }

    return $count;
}

function isInArray($array, $search, $isLowerCase)
{
    if ($array == null || !is_array($array)) {
        return false;
    }

    foreach ($array as $entry) {
        if ($isLowerCase) {
            $entry = strtolower($entry);
        }

        if ($entry == $search) {
            return true;
        }
    }

    return false;
}

function substring($str, $offset, $length = -1, $ellipsis = '')
{
    if ($str != null && strlen($str) > $length - $offset) {
        $str = ($length == -1 ? substr($str, $offset) : substr($str, $offset, $length)) . $ellipsis;
    }

    return $str;
}

function printPath($path, $isHrefEnd = false)
{
    $html = null;

    if ($path != null && $path != '/' && strpos($path, '/') !== false) {
        $array = explode('/', preg_replace('|^/(.*?)$|', '\1', $path));
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
                $html .= '<span class="path_seperator">/</span><a href="index.php?dir=' . rawurlencode($url . $item) . '">';
            } else {
                $html .= '<span class="path_seperator">/</span>';
            }

            $url  .= $item;
            $html .= '<span class="path_entry">' . substring($entry, 0, NAME_SUBSTR, NAME_SUBSTR_ELLIPSIS) . '</span>';

            if ($key < count($array) - 1 || ($key == count($array) - 1 && $isHrefEnd)) {
                $html .= '</a>';
            }
        }
    }

    return $html;
}

function getPathPHP()
{
    if ($path = getenv('PATH')) {
        $array = @explode(strpos($path, ':') !== false ? ':' : PATH_SEPARATOR, $path);

        foreach ($array as $entry) {
            if (strstr($entry, 'php.exe') && isset($_SERVER['WINDIR']) && is_file($entry)) {
                return $entry;
            } else {
                $bin = $entry . DIRECTORY_SEPARATOR . 'php' . (isset($_SERVER['WINDIR']) ? '.exe' : null);

                if (is_file($bin)) {
                    return $bin;
                }
            }
        }
    }

    return 'php';
}

function isFunctionExecEnable()
{
    return function_exists('exec')
        && isFunctionDisable('exec') == false;
}

function isFunctionDisable($func)
{
    $list = @ini_get('disable_functions');

    if (empty($list) == false) {
        $func = strtolower(trim($func));
        $list = explode(',', $list);

        foreach ($list as $e) {
            if (strtolower(trim($e)) == $func) {
                return true;
            }
        }
    }

    return false;
}

// file
function printFileActions(SplFileInfo $file) {
    global $pages, $formats, $dirEncode;

    $name = $file->getFilename();
    $format = $file->getExtension();

    echo '<div class="title">Chức năng</div>
    <ul class="list">';

    if (isFormatText($name)) {
        echo '<li><img src="icon/edit.png"/> <a href="edit_text.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sửa văn bản</a></li>';
        echo '<li><img src="icon/edit_text_line.png"/> <a href="edit_text_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sửa theo dòng</a></li>';
        echo '<li><img src="icon/edit_text_line.png"/> <a href="edit_code.php?dir=' . $dirEncode . '&name=' . $name . '">Chế độ sửa code</a></li>';
        echo '<li><img src="icon/columns.png"/> <a href="view_code.php?dir=' . $dirEncode . '&name=' . $name . '">Xem code</a></li>';
    } elseif (in_array($format, $formats['zip'])) {
        echo '<li><img src="icon/unzip.png"/> <a href="file_viewzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Xem</a></li>';
        echo '<li><img src="icon/unzip.png"/> <a href="file_unzip.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Giải nén</a></li>';
    } elseif (isFormatUnknown($name)) {
        echo '<li><img src="icon/edit.png"/> <a href="edit_text.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sửa dạng văn bản</a></li>';
        echo '<li><img src="icon/edit_text_line.png"/> <a href="edit_text_line.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sửa theo dòng</a></li>';
        echo '<li><img src="icon/columns.png"/> <a href="view_code.php?dir=' . $dirEncode . '&name=' . $name . '">Xem code</a></li>';
    }

    echo '<li><img src="icon/download.png"/> <a href="file_download.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Tải về</a></li>
        <li><img src="icon/rename.png"/> <a href="file_rename.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Đổi tên</a></li>
        <li><img src="icon/copy.png"/> <a href="file_copy.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Sao chép</a></li>
        <li><img src="icon/move.png"/> <a href="file_move.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Di chuyển</a></li>
        <li><img src="icon/delete.png"/> <a href="file_delete.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Xóa</a></li>
        <li><img src="icon/access.png"/> <a href="file_chmod.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Chmod</a></li>
        <li><img src="icon/info.png"/> <a href="file.php?dir=' . $dirEncode . '&name=' . $name . $pages['paramater_1'] . '">Thông tin</a></li>
        <li><img src="icon/list.png"/> <a href="index.php?dir=' . $dirEncode . $pages['paramater_1'] . '">Danh sách</a></li>
    </ul>';
}

function debug($o)
{
    echo('<pre>');
    var_dump($o);
    echo('</pre>');
}

function asset($asset)
{
    return $asset . '?' .  date('YmdHi', filemtime($asset));
}

function cookie(
    $cookie,
    $option = null
) {
    // get
    if (is_string($cookie)) {
        return isset($_COOKIE[$cookie]) ? $_COOKIE[$cookie] : $option;
    }

    // set
    if (is_array($cookie)) {
        $option = is_array($option) ? $option : [];

        foreach ($cookie as $key => $value) {
            setcookie($key, $value, $option);
        }
    }
}

// auth
function getLoginFail() {
    if (file_exists(LOGIN_LOCK)) {
        $timeDifference = time() - filemtime(LOGIN_LOCK);
    
        // reset 30 phut
        if ($timeDifference >= 1800) {
            removeLoginFail();
        }
    }

    return (int) @file_get_contents(LOGIN_LOCK);
}
function increaseLoginFail() {
    file_put_contents(LOGIN_LOCK, getLoginFail() + 1);
}
function removeLoginFail() {
    @unlink(LOGIN_LOCK);
}
function ableLogin() {
    return getLoginFail() < LOGIN_MAX;
}
