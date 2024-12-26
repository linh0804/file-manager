<?php

define('ACCESS', true);

require '.init.php';

setcookie(FM_COOKIE_NAME, '', 0);

goURL('index.php');
