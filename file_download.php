<?php

define('ACCESS', true);
require __DIR__ . '/file.php';

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . basename($curr_path));
header('Content-Length: ' . filesize($curr_path));
readfile($curr_path);
