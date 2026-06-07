<?php

defined('ACCESS') or exit;

if (
    is_file($curr_path)
    && ($info = getimagesize($curr_path)) !== false
) {
    header('Content-Type: ' . $info['mime']);
    header('Content-Length: ' . filesize($curr_path));
    readfile($curr_path);
} else {
    exit('Not read image');
}
