<?php

namespace app;

defined('ACCESS') or exit('Not access');

// auth
function get_login_fail()
{
    $last_login = (int) config()->get(LOGIN_LOCK . '_time');
    $timeDifference = time() - $last_login;

    // reset 30 phut
    if ($timeDifference >= 1800) {
        remove_login_fail();
    }

    return (int) config()->get(LOGIN_LOCK);
}

function increase_login_fail()
{
    config()->set(LOGIN_LOCK, get_login_fail() + 1);
    config()->set(LOGIN_LOCK . '_time', time());
}

function remove_login_fail()
{
    config()->set(LOGIN_LOCK, 0);
    config()->set(LOGIN_LOCK . '_time', 0);
}

function able_login()
{
    return get_login_fail() < LOGIN_MAX;
}
