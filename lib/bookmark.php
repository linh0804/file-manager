<?php

namespace app;

defined('ACCESS') or exit('Not access');

function bookmark_get()
{
    return config()->get('bookmarks', []);
}

function bookmark_save(array $data)
{
    config()->set('bookmarks', $data);
}

function bookmark_add($path)
{
    $bookmarks = bookmark_get();
    $bookmarks[] = $path;
    $bookmarks = array_unique($bookmarks);

    bookmark_save($bookmarks);
}

function bookmark_delete($path)
{
    $bookmarks = bookmark_get();
    $bookmarks = array_diff(
        $bookmarks,
        [ $path ]
    );

    bookmark_save($bookmarks);
}
