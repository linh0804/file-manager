<?php

if (
    !defined('ACCESS')
    || !defined('PHPMYADMIN')
    || !defined('REALPATH')
    || !defined('PATH_DATABASE')
    || !defined('LINK_IDENTIFIER')
) {
    die('Not access');
}

if (LINK_IDENTIFIER != false) {
    @mysqli_close($MySQLi);
}
