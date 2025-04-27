<?php

defined('ACCESS') or exit('Not access');

@ini_set('display_errors', true);
@ini_set('memory_limit', -1);
@ini_set('max_execution_time', 0);
@ini_set('log_errors', 'On');
@ini_set('error_log', __DIR__ . '/error_log');
@ini_set('opcache.enable', false);

error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR);

// no cache
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Check require function
{
    $require = [
        'curl_init',
        'filter_var',
        'json_encode',
        'json_decode',
        'getenv'
    ];

    foreach ($require as $function) {
        if (!function_exists($function)) {
            exit($function . '() not found');
        }
    }
}

define('REALPATH', realpath('./'));
define('rootPath', __DIR__);

define('isBuiltinServer', php_sapi_name() === 'cli-server');

define('isHttps', isset($_SERVER['HTTPS']));
define('requestScheme', isHttps ? 'https' : 'http');

define('baseFolder', basename(dirname($_SERVER['SCRIPT_FILENAME'])));
define('baseUrl', requestScheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . (isBuiltinServer ? '' : '/' . baseFolder));

// load thu vien
require rootPath . '/vendor/autoload.php';
require rootPath . '/lib/function.php';
require rootPath . '/lib/zip.class.php';

// tải tài nguyên
$icon = rootPath . '/json/icon.json';
if (!file_exists($icon)) {
    file_put_contents($icon, file_get_contents('https://cdn.ngatngay.net/icon/atom.json'));
}
define('icons', json_decode(file_get_contents($icon), true));

// tạo tmp nếu chưa có
{
    $tmp_dir = rootPath . '/tmp';
    $tmp_file = $tmp_dir . '/.htaccess';

    if (!is_dir($tmp_dir)) {
        mkdir($tmp_dir);
    }

    if (!file_exists($tmp_file)) {
        file_put_contents(
            $tmp_file,
            'deny from all'
        );
    }

    unset($tmp_dir);
    unset($tmp_file);
}

// cau hinh
const PATH_CONFIG = rootPath . '/config.inc.php';

define('pathConfig', rootPath . '/config.inc.php');
define('pathDatabase', rootPath . '/config.db.inc.php');

const LOGIN_USERNAME_DEFAULT = 'Admin';
const LOGIN_PASSWORD_DEFAULT = '12345';

const LOGIN_LOCK = 'login_fail';
const LOGIN_MAX = 5;

const PAGE_LIST_DEFAULT = 1000;
const PAGE_FILE_EDIT_DEFAULT = 1000000;
const PAGE_FILE_EDIT_LINE_DEFAULT = 100;
const PAGE_DATABASE_LIST_ROWS_DEFAULT = 100;

const PAGE_NUMBER      = 7;
const PAGE_URL_DEFAULT = 'default';
const PAGE_URL_START   = 'start';
const PAGE_URL_END     = 'end';

const DEVELOPMENT          = false;
const NAME_SUBSTR          = 1000;
const NAME_SUBSTR_ELLIPSIS = '...';

const FM_COOKIE_NAME = 'fm_php';

{ // lay thong tin phien ban hien tai
    $version = json_decode(
        file_get_contents(rootPath . '/version.json'),
        true
    );

    define('VERSION_MAJOR', $version['major']);
    define('VERSION_MINOR', $version['minor']);
    define('VERSION_PATCH', $version['patch']);
    define('localVersion', $version['version']);

    unset($version);
}

{ // lay phien ban moi
    define('remoteVersionFile', 'https://raw.githubusercontent.com/ngatngay/file-manager/main/version.json');
    define('remoteFile', 'https://raw.githubusercontent.com/ngatngay/file-manager/refs/heads/main/file-manager.zip');
    define('REMOTE_FILE', 'https://github.com/ngatngay/file-manager/archive/main.zip');
    define('REMOTE_DIR_IN_ZIP', 'file-manager-main');

    $version = getNewVersion();
    $remoteVersion = localVersion;

    if ($version !== false) {
        $remoteVersion = $version['version'];
    }

    define('remoteVersion', $remoteVersion);
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

define('isLogin', $isLogin);

if (
    !isLogin
    && !defined('LOGIN')
) {
    goURL('login.php');
}

// kiem tra nguoi dung
$script = function_exists('getenv') ? getenv('SCRIPT_NAME') : $_SERVER['SCRIPT_NAME'];
$script = strpos($script, '/') !== false ? dirname($script) : '';
$script = str_replace('\\', '/', $script);

define('IS_INSTALL_ROOT_DIRECTORY', $script == '.' || $script == '/');
define('IS_ACCESS_FILE_IN_FILE_MANAGER', defined('INDEX') && isset($_GET['not']));
define('DIRECTORY_FILE_MANAGER', strpos($script, '/') !== false ? @substr($script, strrpos($script, '/') + 1) : null);
define('PATH_FILE_MANAGER', str_replace('\\', '/', strtolower($_SERVER['DOCUMENT_ROOT'] . $script)));
define('NAME_DIRECTORY_INSTALL_FILE_MANAGER', !IS_INSTALL_ROOT_DIRECTORY ? preg_replace('#(/+|/\+)(.+?)#s', '$2', $script) : null);
define('PARENT_PATH_FILE_MANAGER', substr(PATH_FILE_MANAGER, 0, strlen(PATH_FILE_MANAGER) - (NAME_DIRECTORY_INSTALL_FILE_MANAGER == null ? 0 : strlen(NAME_DIRECTORY_INSTALL_FILE_MANAGER) + 1)));

if (
    IS_INSTALL_ROOT_DIRECTORY ||
    IS_ACCESS_FILE_IN_FILE_MANAGER ||

    ($script != '.' && $script != '/' && isPathNotPermission(processDirectory($dir))) ||
    ($script != '.' && $script != '/' && $name != null && isPathNotPermission(processDirectory($dir . '/' . $name)))
) {
    define('NOT_PERMISSION', true);
} else {
    define('NOT_PERMISSION', false);
}

if (
    !defined('INDEX')
    && !defined('LOGIN')
    && NOT_PERMISSION
) {
    //goURL('index.php?not');
}

if (!empty($dir)) {
    define(
        'IS_ACCESS_PARENT_PATH_FILE_MANAGER',
        strtolower(processDirectory($dir)) == strtolower(processDirectory(PARENT_PATH_FILE_MANAGER))
    );
} else {
    define(
        'IS_ACCESS_PARENT_PATH_FILE_MANAGER',
        strtolower(processDirectory(PARENT_PATH_FILE_MANAGER)) == strtolower(processDirectory($_SERVER['DOCUMENT_ROOT']))
    );
}

function isPathNotPermission($path, $isUseName = false): bool
{
    if (!empty($path)) {
        $reg  = $isUseName ? NAME_DIRECTORY_INSTALL_FILE_MANAGER : PATH_FILE_MANAGER;
        $reg  = $reg != null ? strtolower($reg) : null;
        $path = str_replace('\\', '/', $path);
        $path = strtolower($path);

        if (preg_match('#^' . $reg . '$#si', $path)) {
            return true;
        } elseif (preg_match('#^' . $reg . '/(^\/+|^\\+)(.*?)$#si', $path)) {
            return true;
        } elseif (preg_match('#^' . $reg . '/(.*?)$#si', $path)) {
            return true;
        }

        return false;
    }

    return false;
}

unset($script);

// Kiểm tra thư mục cài đặt
if (IS_INSTALL_ROOT_DIRECTORY) {
    $title = 'Lỗi File Manager';

    include_once 'header.php';
    echo '<div class="title">Lỗi File Manager</div>
        <div class="list">Bạn đang cài đặt File Manager trên thư mục gốc, hãy chuyển vào một thư mục khác!</div>';
    include_once 'footer.php';
    exit();
}

function encodePath($path)
{
    return base64_encode($path);
}
function decodePath($path)
{
    //$path =
    $path = str_replace('\\', '/', $path);
}

$path = processDirectory((string) $dir, true) . processName((string) $name);
$path = file_exists($path) ? $path : '';
$file = new SplFileInfo($path);

// bookmark
$add_bookmark = isset($_GET['add_bookmark']) ? trim($_GET['add_bookmark']) : '';
if (!empty($add_bookmark)) {
    $add_bookmark = rawurldecode($add_bookmark);

    if (is_dir($add_bookmark)) {
        bookmark_add($add_bookmark);
        goURL('index.php?dir=' . $add_bookmark);
    }
}

$delete_bookmark = isset($_GET['delete_bookmark']) ? trim($_GET['delete_bookmark']) : '';
if (!empty($delete_bookmark)) {
    bookmark_delete(rawurldecode($delete_bookmark));
    goURL('index.php');
}
