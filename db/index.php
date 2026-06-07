<?php

$app_name = 'file_manager_' . md5(dirname(__DIR__) . '/_init.php');
isset($_COOKIE[$app_name . '_auth']) or exit;

error_reporting(0);
require __DIR__ . '/admin';
