<?php

use nightmare\http\request;
use nightmare\json;

defined('ACCESS') or exit;

@ini_set('display_errors', true);
@ini_set('memory_limit', -1);
@ini_set('max_execution_time', 3600);
@ini_set('opcache.revalidate_freq', 2);

error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR);

ob_start();

// no cache
header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// constants
define('APP_NAME', 'file_manager_' . md5(__FILE__));
define('APP_CONFIG_FILE', __DIR__ . '/.env.php');

// load thu vien
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/_function.php';

$version = json::decode_file(__DIR__ . '/version.json');
define('APP_VERSION', $version['version']);

// cau hinh
define('LOGIN_USERNAME_DEFAULT', 'admin');
define('LOGIN_PASSWORD_DEFAULT', '!!!123456789!!!');
define('LOGIN_MAX', 10);
define('LOGIN_WAIT', 3600);

define('PAGE_SIZE', 200);
define('PAGE_NUMBER', 10);
define('PAGE_URL_DEFAULT', 'default');
define('PAGE_URL_START', 'start');
define('PAGE_URL_END', 'end');

// lay phien ban moi
define('REMOTE_VERSION_URL', 'https://static.nightmare.top/app/file-manager-php/release.json');
define('REMOTE_FILE_URL', 'https://static.nightmare.top/app/file-manager-php/release.zip');

define('COMMON_FILE_FORMAT', [
    'image' => ['png', 'ico', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'],
    'text' => ['cpp', 'css', 'csv', 'h', 'htaccess', 'html', 'java', 'js', 'lng', 'pas', 'php', 'pl', 'py', 'rb', 'rss', 'sh', 'svg', 'tpl', 'txt', 'xml', 'ini', 'cnf', 'config', 'conf', 'conv'],
    'archive' => ['7z', 'rar', 'tar', 'tarz', 'zip'],
    'audio' => ['acc', 'midi', 'mp3', 'mp4', 'swf', 'wav'],
    'font' => ['afm', 'bdf', 'otf', 'pcf', 'snf', 'ttf'],
    'binary' => ['pak', 'deb', 'dat'],
    'document' => ['pdf'],
    'source' => ['changelog', 'copyright', 'license', 'readme'],
    'zip' => ['zip', 'jar', 'rar'],
    'other' => ['rpm', 'sql']
]);
define('COMMON_FILE_EXCLUDES', [
    '.git/',
    'node_modules/',
    'vendor/',
    'asset/',
    'assets/',
    'files/'
]);

define('SITE_TITLE', 'File Manager');
define('SITE_HEADER', __DIR__ . '/_header.php');
define('SITE_FOOTER', __DIR__ . '/_footer.php');

$pages = array(
    'current' => 1,
    'total' => 0,
    'paramater_0' => null,
    'paramater_1' => null
);

// check cấu hình
if (
    empty(config()->get('username'))
    || empty(config()->get('password'))
) {
    define('IS_CONFIG_ERROR', true);
} else {
    define('IS_CONFIG_ERROR', false);
}

// Kiểm tra đăng nhập
if (IS_CONFIG_ERROR) {
    define('IS_LOGIN', false);
} else {
    $is_login_cookie = $_COOKIE[APP_NAME . '_auth'] ?? '';
    $is_login = !empty($is_login_cookie) && $is_login_cookie === config()->get('password');

    if (getenv('FILE_MANAGER_PHP_AUTO_LOGIN') === 'on') {
        $is_login = true;
        @setcookie(APP_NAME . '_auth', 'autologin', time() + 3600 * 24 * 365);
    }

    define('IS_LOGIN', $is_login);
}

if (!IS_LOGIN) {
    if (!defined('LOGIN_BYPASS_AUTO_REDIRECT')) {
        redirect(action_link('login'));
    }
}

// Phân trang

if (
    PAGE_SIZE > 0
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



if (!IS_LOGIN && !auth_can_login()) {
    exit('đăng nhập sai nhiều lần, cấm 1 giờ');
}
