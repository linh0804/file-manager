<?php
namespace app;

define('ACCESS', true);

include '_init.php';

$path = !empty($_GET['path']) ? rawurldecode((string) $_GET['path']) : '';

if (
    isLogin
    && is_file($path)
    && getimagesize($path) !== false
) {
    readfile($path);
} else {
    exit('Not read image');
}
