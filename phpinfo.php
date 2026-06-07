<?php

$app_name = 'file_manager_' . md5(__DIR__ . '/_init.php');
isset($_COOKIE[$app_name . '_auth']) or exit;

phpinfo();
