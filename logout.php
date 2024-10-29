<?php define('ACCESS', true);

    include_once '.init.php';

    setcookie(FM_COOKIE_NAME, '', 0);
    goURL('index.php');
    
    /*
    if (IS_LOGIN) {
        setcookie(FM_COOKIE_NAME, '', 0);

        $ref = $_SERVER['REQUEST_URI'];
        $ref = $ref != $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ? $ref : null;

        if (IS_LOGIN)
            goURL('login.php');
        else
            goURL($ref != null ? $ref : 'index.php');
    } else {
        goURL('login.php');
    }
    */
