<?php

defined('ACCESS') or exit('Not access');

session_start();
ob_start();

if (!defined('DONT_LOAD_INI_SET')) {
    @ini_set('display_errors', true);
    @ini_set('display_startup_errors', true);
    @ini_set('memory_limit', -1);
    @ini_set('max_execution_time', 0);
    @ini_set('opcache.enable', false);
}

error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR);

header('Content-Type:text/html;charset=utf-8');

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
define('rootPath', realpath('./'));

define('isBuiltinServer', php_sapi_name() === 'cli-server');

define('isHttps', isset($_SERVER['HTTPS']));
define('requestScheme', isHttps ? 'https' : 'http');

define('baseFolder', basename(dirname($_SERVER['SCRIPT_FILENAME'])));
define('baseUrl', requestScheme . '://' . $_SERVER['HTTP_HOST'] . (isBuiltinServer ? '' : '/' . baseFolder));

// require function
require_once 'lib/functions.php';

// tạo tmp nếu chưa có
{
    $tmp_dir = __DIR__ . '/tmp';
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

{
    $dir = getenv('SCRIPT_NAME');
    $dir = str_replace('\\', '/', $dir);
    $dir = strpos($dir, '/') !== false ? dirname($dir) : '';
    $dir = str_replace('\\', '/', $dir);
    $dir = $dir == '.' || $dir == '/' ? '' : $dir;

    $_SERVER['DOCUMENT_ROOT'] = realpath('.');
    $_SERVER['DOCUMENT_ROOT'] = !$dir ? $_SERVER['DOCUMENT_ROOT'] : substr($_SERVER['DOCUMENT_ROOT'], 0, strlen($_SERVER['DOCUMENT_ROOT']) - strlen($dir));
    $_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);

    unset($dir);
}

// cau hinh
const PATH_CONFIG = 'config.inc.php';
const PATH_DATABASE = 'database.inc.php';

const LOGIN_USERNAME_DEFAULT = 'Admin';
const LOGIN_PASSWORD_DEFAULT = '12345';

const LOGIN_LOCK = __DIR__ . '/login_fail.lock';
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
        file_get_contents('version.json'),
        true
    );
    
    define('VERSION_MAJOR', $version['major']);
    define('VERSION_MINOR', $version['minor']);
    define('VERSION_PATCH', $version['patch']);
    define('VERSION_MESSAGE', $version['message']);
    
    unset($version);
}

{ // lay phien ban moi
    define('REMOTE_FILE', 'https://github.com/ngatngay/file-manager/archive/main.zip');
	define('REMOTE_FILE_CURRENT', 'https://github.com/ngatngay/file-manager/archive/refs/tags/' . VERSION_MAJOR . '.' . VERSION_MINOR . '.' . VERSION_PATCH . '.zip');
    define('REMOTE_DIR_IN_ZIP', 'file-manager-main');
    define('REMOTE_VERSION_FILE', 'https://raw.githubusercontent.com/ngatngay/file-manager/main/version.json');

	$version = getNewVersion();
	$remoteFileNew = REMOTE_FILE_CURRENT;

	if ($version !== false) {
		$remoteFileNew = 'https://github.com/ngatngay/file-manager/archive/refs/tags/' . $version['major'] . '.' . $version['minor'] . '.' . $version['patch'] . '.zip';
	}

	define('REMOTE_FILE_NEW', $remoteFileNew);

	unset($remoteFileNew);
	unset($version);
}

$configs = array();

$pages = array(
    'current'     => 1,
    'total'       => 0,
    'paramater_0' => null,
    'paramater_1' => null
);

$formats = array(
    'image'    => array('png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp'),
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
        empty($configs['password']) || $configs['password'] == null)
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

$isLogin = isset($_COOKIE[FM_COOKIE_NAME])
    && isset($configs['password'])
    && $_COOKIE[FM_COOKIE_NAME] === $configs['password'];

define('IS_LOGIN', $isLogin);

include_once 'development.inc.php';

$dir = isset($_GET['dir']) && !empty($_GET['dir']) ? rawurldecode($_GET['dir']) : null;
$name = isset($_GET['name']) && !empty($_GET['name']) ? $_GET['name'] : null;
$dirEncode = !empty($dir) ? rawurlencode($dir) : '';

require_once 'permission.inc.php';

// load thu vien
$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
    include_once $autoload;
}
