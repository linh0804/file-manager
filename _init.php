<?php

namespace app;

use nightmare\http\http;
use nightmare\http\request;
use nightmare\json;
use SplFileInfo;

defined('ACCESS') or exit('Not access');

@ini_set('display_errors', true);
@ini_set('memory_limit', -1);
@ini_set('max_execution_time', 3600);
@ini_set('opcache.revalidate_freq', 0);

error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR);

ob_start();

// no cache
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// constants
define('ROOT_PATH', __DIR__);
define('TMP_PATH', __DIR__ . '/tmp');

define('IS_BUILTIN_SERVER', php_sapi_name() === 'cli-server');

define('IS_HTTPS', isset($_SERVER['HTTPS']));
define('REQUEST_SCHEME', IS_HTTPS ? 'https' : 'http');

define('BASE_FOLDER', basename(dirname($_SERVER['SCRIPT_FILENAME'])));
define('BASE_URL', REQUEST_SCHEME . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . (IS_BUILTIN_SERVER ? '' : '/' . BASE_FOLDER));

// load thu vien
require ROOT_PATH . '/vendor/autoload.php';
require ROOT_PATH . '/lib/function.php';

// tạo tmp nếu chưa có
{
    $tmp_dir = ROOT_PATH . '/tmp';
    $tmp_file = $tmp_dir . '/.htaccess';

    if (!is_dir($tmp_dir)) {
        mkdir($tmp_dir);
    }

    if (!file_exists($tmp_file)) {
        file_put_contents($tmp_file, 'Require all denied');
    }
}

// tải tài nguyên
define('ICONS', [
    'files' => load_json_remote('https://static.ngatngay.net/atom-icon/files/__struct__.json', 'icon/icon_files.json'),
    'folders' => load_json_remote('https://static.ngatngay.net/atom-icon/folders/__struct__.json', 'icon/icon_folders.json')
]);

// cau hinh
define('PATH_CONFIG', ROOT_PATH . '/config.inc.php');

define('LOGIN_USERNAME_DEFAULT', 'Admin');
define('LOGIN_PASSWORD_DEFAULT', '12345');

define('LOGIN_LOCK', 'login_fail');
define('LOGIN_MAX', 5);

define('PAGE_LIST_DEFAULT', 1000);
define('PAGE_FILE_EDIT_DEFAULT', 1000000);
define('PAGE_FILE_EDIT_LINE_DEFAULT', 100);
define('PAGE_DATABASE_LIST_ROWS_DEFAULT', 100);

define('PAGE_NUMBER', 7);
define('PAGE_URL_DEFAULT', 'default');
define('PAGE_URL_START', 'start');
define('PAGE_URL_END', 'end');

define('NAME_SUBSTR', 1000);
define('NAME_SUBSTR_ELLIPSIS', '...');

define('FM_COOKIE_NAME', 'fm_php');

{
    // lay thong tin phien ban hien tai
    $version = json::decode_file(ROOT_PATH . '/version.json');

    define('VERSION_MAJOR', $version['major']);
    define('VERSION_MINOR', $version['minor']);
    define('VERSION_PATCH', $version['patch']);
    define('LOCAL_VERSION', $version['version']);

    // lay phien ban moi
    define('REMOTE_VERSION_FILE', 'https://static.ngatngay.net/php/file-manager/release.json');
    define('REMOTE_FILE_URL', 'https://static.ngatngay.net/php/file-manager/release.zip');
    define('REMOTE_FILE', 'https://static.ngatngay.net/php/file-manager/release.zip');
    define('REMOTE_DIR_IN_ZIP', 'file-manager-main');

    // check định kỳ 6 tiếng
    define('REMOTE_VERSION_API', 'https://static.ngatngay.net/php/file-manager/release.json');
    $tmp = TMP_PATH . '/_version.json';

    if (
        !file_exists($tmp)
        || (file_exists($tmp) && filemtime($tmp) < (time() - 6*3600))
    ) {
        @download_file($tmp, REMOTE_VERSION_API);
    }
    
    $remote_version = @json::decode_file($tmp);
    define('REMOTE_VERSION', empty($remote_version) ? LOCAL_VEREION : $remote_version['version']);
    define('HAS_NEW_VERSION', version_compare((string) REMOTE_VERSION, (string) LOCAL_VERSION, '>'));
}

$configs = [];
$pages = array(
    'current' => 1,
    'total' => 0,
    'paramater_0' => null,
    'paramater_1' => null
);

$formats = array(
    'image'    => array('png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'),
    'text'     => array('cpp', 'css', 'csv', 'h', 'htaccess', 'html', 'java', 'js', 'lng', 'pas', 'php', 'pl', 'py', 'rb', 'rss', 'sh', 'svg', 'tpl', 'txt', 'xml', 'ini', 'cnf', 'config', 'conf', 'conv'),
    'archive'  => array('7z', 'rar', 'tar', 'tarz', 'zip'),
    'audio'    => array('acc', 'midi', 'mp3', 'mp4', 'swf', 'wav'),
    'font'     => array('afm', 'bdf', 'otf', 'pcf', 'snf', 'ttf'),
    'binary'   => array('pak', 'deb', 'dat'),
    'document' => array('pdf'),
    'source'   => array('changelog', 'copyright', 'license', 'readme'),
    'zip'      => array('zip', 'jar'),
    'other'    => array('rpm', 'sql')
);
$excludeDirDefault = implode("\n", [
    '.git/',
    'node_modules/',
    'vendor/',
    'asset/',
    'assets/',
    'files/'
]);

if (is_file(PATH_CONFIG)) {
    include PATH_CONFIG;
}

if (count($configs) == 0) {
    setcookie(FM_COOKIE_NAME, '', 0);
    redirect('/');
}

if (
    !isset($configs['username']) ||
    !isset($configs['password']) ||
    !isset($configs['page_list']) ||
    !isset($configs['page_file_edit']) ||
    !isset($configs['page_file_edit_line']) ||
    !isset($configs['page_database_list_rows'])
) {
    define('IS_CONFIG_UPDATE', true);
} else {
    define('IS_CONFIG_UPDATE', false);
}

if (!IS_CONFIG_UPDATE && (
    !preg_match('#\\b[0-9]+\\b#', $configs['page_list']) ||
        !preg_match('#\\b[0-9]+\\b#', $configs['page_file_edit']) ||
        !preg_match('#\\b[0-9]+\\b#', $configs['page_file_edit_line']) ||
        !preg_match('#\\b[0-9]+\\b#', $configs['page_database_list_rows']) ||

        empty($configs['username']) || $configs['username'] == null ||
        empty($configs['password']) || $configs['password'] == null
)
) {
    define('IS_CONFIG_ERROR', true);
} else {
    define('IS_CONFIG_ERROR', false);
}

if (IS_CONFIG_UPDATE || IS_CONFIG_ERROR) {
    setcookie(FM_COOKIE_NAME, '', 0);
    redirect('/');
}

if (
    isset($configs['page_list'])
    && $configs['page_list'] > 0
    && isset($_GET['page_list'])
) {
    $pages['current'] = intval($_GET['page_list']) <= 0
        ? 1
        : intval($_GET['page_list']);

    if ($pages['current'] > 1) {
        $pages['paramater_0'] = '?page_list=' . $pages['current'];
        $pages['paramater_1'] = '&page_list=' . $pages['current'];
    }
}

$dir = !empty($_GET['dir']) ? rawurldecode($_GET['dir']) : null;
$name = !empty($_GET['name']) ? $_GET['name'] : null;
$dirEncode = !empty($dir) ? rawurlencode($dir) : '';

// Kiểm tra đăng nhập
$isLogin = isset($_COOKIE[FM_COOKIE_NAME])
    && isset($configs['password'])
    && $_COOKIE[FM_COOKIE_NAME]
    === $configs['password'];

define('IS_LOGIN', $isLogin);

if (
    !IS_LOGIN
    && !defined('LOGIN')
) {
    redirect('login.php');
}

// Kiểm tra thư mục cài đặt
if (is_in_root()) {
    $title = 'Lỗi File Manager';

    require_once '_header.php';
    echo '<div class="title">Lỗi File Manager</div>
        <div class="list">Bạn đang cài đặt File Manager trên thư mục gốc, hãy chuyển vào một thư mục khác!<br><br><i><b>' . __DIR__ . '</b></i></div>';
    require_once '_footer.php';
    exit();
}

$path = rawurldecode((string) request::get('path'));
$file = new SplFileInfo($path);

// referer
function remove_referer_param(string $url): string {
    // Parse URL
    $parts = parse_url($url);

    // Nếu không có query thì trả nguyên
    if (!isset($parts['query'])) {
        return $url;
    }

    // Parse các tham số query
    parse_str($parts['query'], $queryParams);

    // Xoá tham số 'referer'
    unset($queryParams['referer']);

    // Build lại query string
    $newQuery = http_build_query($queryParams);

    // Build lại URL
    $cleanUrl = $parts['path'];
    if ($newQuery) {
        $cleanUrl .= '?' . $newQuery;
    }

    return $cleanUrl;
}

$referer_qs = base64_encode(remove_referer_param(request::uri()));
define('REFERER_QS', 'referer=' . $referer_qs);

$referer = (string) request::get('referer');
define('REFERER', base64_decode($referer));

// bookmark
$add_bookmark = isset($_GET['add_bookmark']) ? trim($_GET['add_bookmark']) : '';
if (!empty($add_bookmark)) {
    $add_bookmark = rawurldecode($add_bookmark);

    if (is_dir($add_bookmark)) {
        bookmark_add($add_bookmark);
        redirect('index.php?path=' . $add_bookmark);
    }
}

$delete_bookmark = isset($_GET['delete_bookmark']) ? trim($_GET['delete_bookmark']) : '';
if (!empty($delete_bookmark)) {
    bookmark_delete(rawurldecode($delete_bookmark));
    redirect('index.php');
}
