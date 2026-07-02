<?php

define('ACCESS', true);
require __DIR__ . '/_init.php';

setcookie(
    APP_NAME . '_auth',
    'logout',
    time() - 3600,
    '/'
);

redirect(action_link('index'));
