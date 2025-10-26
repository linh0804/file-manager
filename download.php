<?php

namespace app;

define('ACCESS', true);

require '_init.php';

$path = get_path();
check_path($path);

header('Content-Type: application/octet-stream');
header('Content-Disposition: inline; filename=' . basename($path));
header('Content-Length: ' . filesize($path));
readfile($path);
