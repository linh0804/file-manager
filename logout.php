<?php

define('ACCESS', true);

require __DIR__ . '/_init.php';

setcookie(APP_NAME . '_auth', '', 0);

redirect(action_link('index'));
