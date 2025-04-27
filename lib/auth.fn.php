<?php

// auth
function getLoginFail()
{
    $last_login = (int) config()->get(LOGIN_LOCK . '_time');
    $timeDifference = time() - $last_login;

    // reset 30 phut
    if ($timeDifference >= 1800) {
        removeLoginFail();
    }

    return (int) config()->get(LOGIN_LOCK);
}

function increaseLoginFail()
{
    config()->set(LOGIN_LOCK, getLoginFail() + 1);
    config()->set(LOGIN_LOCK . '_time', time());
}

function removeLoginFail()
{
    config()->set(LOGIN_LOCK, 0);
    config()->set(LOGIN_LOCK . '_time', 0);
}

function ableLogin()
{
    return getLoginFail() < LOGIN_MAX;
}
