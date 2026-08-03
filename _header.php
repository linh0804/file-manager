<?php

defined('ACCESS') or exit;

$site_title = $site_title ?? SITE_TITLE;
$site_sidebar = '';
$header_goto_path = get_curr_path();

if (IS_LOGIN) {
    $header_goto_path = !empty($header_goto_path) ? $header_goto_path : '';
    $header_goto_path = (string) $header_goto_path;

    if ($header_goto_path !== '/') {
        $header_goto_path = rtrim($header_goto_path, '/');

        if (is_dir($header_goto_path)) {
            $header_goto_path .= '/';
        }
    }
}
?><!DOCTYPE html>
<html lang="vi">

<head>
    <title><?= htmlspecialchars((string) $site_title) ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="icon/icon.png">
    <link rel="icon" type="image/x-icon" href="icon/icon.ico" />
    <link rel="shortcut icon" type="image/x-icon" href="icon/icon.ico" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/jquery-ui@1.14.2/dist/jquery-ui.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/jquery-ui@1.14.2/themes/base/base.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/jquery-ui@1.14.2/themes/base/theme.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

    <link rel="stylesheet" type="text/css" href="<?= asset('style.css') ?>" media="all,handheld" />
    <link rel="stylesheet" type="text/css" href="<?= asset('js/nightmare-scrolltop.css') ?>" media="all,handheld" />

    <script>const APP_NAME = '<?= APP_NAME ?>';</script>
    <script>
        $(document).on("ajaxStart", function() {
            NProgress.start();
        });
        $(document).on("ajaxError", function() {
            alert("Lỗi server!");
        });
        $(document).on("ajaxStop", function() {
            NProgress.done();
        });
    </script>

    <script src="<?= asset('js/app.js') ?>" defer></script>
    <script src="<?= asset('js/nightmare-scrolltop.js') ?>"></script>
    <script src="<?= asset('js/edit_recent.js') ?>"></script>
</head>

<body>

<div id="app">

<div id="app-header">
    <ul>
        <?php if (IS_LOGIN) { ?>
            <button id="nav-menu">&#9776;</button>
        <?php } ?>
        <li><a href="<?= act_link('index', ['page_list' => null]) ?>"><img src="icon/home.png" /></a></li>
        <?php if (IS_LOGIN) { ?>
            <li><a href="db/"><img src="icon/database.png"/></a></li>
            <li><a href="<?= act_link('setting') ?>"><img src="icon/setting.png" /></a></li>
            <li>
                <img id="header-goto-path-toggle" src="icon/search.png" alt="Goto path" role="button" tabindex="0" aria-controls="header-goto-path-form" data-status="off" />
            </li>
        <?php } ?>
    </ul>
    <?php if (IS_LOGIN) { ?>
        <form id="header-goto-path-form" action="<?= act_link('index', ['page_list' => null]) ?>" method="get">
            <input id="header-goto-path" name="path" type="text" value="<?= htmlspecialchars($header_goto_path) ?>">
            <input type="submit" value="GO">
        </form>
    <?php } ?>
    <div style="clear: both"></div>
</div>

<div id="app-body">

<?php if (IS_LOGIN) { ?>
    <link rel="stylesheet" type="text/css" href="<?= asset('js/app_header_path_autocomplete.css') ?>" />
    <script src="<?= asset('js/app_header_path_autocomplete.js') ?>" defer></script>
<?php } ?>
