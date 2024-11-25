<?php

if (
    !defined('ACCESS')
    || !defined('PHPMYADMIN')
    || !defined('rootPath')
    || !defined('pathDatabase')
    || !defined('LINK_IDENTIFIER')
) {
    die('Not access');
}

if (LINK_IDENTIFIER != false) {
    @mysqli_close($MySQLi);
}
