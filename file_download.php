<?php

define('ACCESS', true);

require '.init.php';

$title = 'Tải tập tin';

check_path($path, 'file');

// down
header('Content-Type: application/octet-stream');
header('Content-Disposition: inline; filename=' . basename($path));
header('Content-Length: ' . filesize($path));
readfile($path);
