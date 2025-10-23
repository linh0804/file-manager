<?php
namespace app;

define('ACCESS', true);

require '_init.php';

setcookie(FM_COOKIE_NAME, '', 0);

redirect('index.php');
