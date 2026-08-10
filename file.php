<?php

defined('ACCESS') or exit;

require __DIR__ . '/_init.php';

$curr_path = get_curr_path();
$curr_file = new SplFileInfo($curr_path);

$site_title = 'File';

check_path($curr_path);
