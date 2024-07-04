<?php

const ACCESS = true;

include 'function.php';

$path = !empty($_GET['path']) ? rawurldecode($_GET['path']) : '';

if (
    IS_LOGIN
    && is_file($path)
    && getimagesize($path) !== false
) {
    readfile($path);
} else {
    exit('Not read image');
}
