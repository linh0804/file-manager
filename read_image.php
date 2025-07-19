<?php

const ACCESS = true;

include '.init.php';

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
