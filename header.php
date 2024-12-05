<?php

if (!defined('ACCESS')) die('Not access');

$menuToggle = '';
?><!DOCTYPE html>
<html lang="vi">

<head>
    <title><?= $title ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="<?= asset('css/style.css') ?>" media="all,handheld" />
    <link rel="icon" type="image/png" href="icon/icon.png">
    <link rel="icon" type="image/x-icon" href="icon/icon.ico" />
    <link rel="shortcut icon" type="image/x-icon" href="icon/icon.ico" />
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdn.jsdelivr.net/npm/@ngatngay/jquery.plugin/dist/app.min.js"></script>
    <script src="<?= asset('js/script.js') ?>" defer></script>
</head>

<body>

<?php
    if (IS_LOGIN) {
    	if (isset($_GET['dev'])) {
    	    setcookie('dev', 1, time() + 86400 + 365);
    	}
        if (isset($_GET['undev'])) {
    	    setcookie('dev', 1, time() - 1);
    	}
    }
?>
<?php if (IS_LOGIN && isset($_COOKIE['dev'])) { ?>
	<script src="https://cdn.jsdelivr.net/npm/eruda"></script>
    <script>eruda.init();</script>
<?php } ?>

<div id="header">
    <ul>
        <?php if (IS_LOGIN) { ?>
            <button id="nav-menu">&#9776;</button>
        <?php } ?>
        <li><a href="index.php"><img src="icon/home.png" /></a></li>
        <?php if (IS_LOGIN) { ?>            
            <?php if (!defined('IS_CONNECT')) { ?>
                <li><a href="database.php"><img src="icon/database.png"/></a></li>
            <?php } else { ?>
                <li><a href="database_disconnect.php"><img src="icon/disconnect.png"/></a></li>
            <?php } ?>
            <li><a href="setting.php"><img src="icon/setting.png" /></a></li>
            <li><a href="logout.php"><img src="icon/exit.png" /></a></li>
        <?php } ?>
    </ul>
    <div style="clear: both"></div>
</div>
<div id="loader-on-fetch" class="spinner-on-fetch" style="display: none;"></div>

<div id="container">

<?php if (IS_LOGIN && hasNewVersion()) { ?>
    <div class="tips" style="margin-top: 0 !important">
        <img src="icon/tips.png" alt="">
        Có phiên bản mới! <a href="update.php"><span style="font-weight: bold; font-style: italic">Cập nhật</span></a> ngay!
    </div>
<?php } ?>

<script>
    // loader on load
function showLoaderOnFetch() {
    document.getElementById("loader-on-fetch").style.display = "block";
    document.querySelector('#boxOverlay').style.display = "block";
    document.body.style.overflowY = "hidden";
}
function hideLoaderOnFetch() {
    document.getElementById("loader-on-fetch").style.display = "none";
    document.querySelector('#boxOverlay').style.display = "none";
    document.body.style.overflowY = "auto";
}
const originalFetch = window.fetch;
window.fetch = async function (...args) {
    showLoaderOnFetch();

    try {
        const response = await originalFetch(...args);
        return response;
    } finally {
        hideLoaderOnFetch();
    }
};
</script>
<style>
.spinner-on-fetch {
  border: 5px solid #1e9fff;
  border-top: 5px solid transparent;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
  z-index: 9999;
    
  /*
  position: fixed;
  top: 10px;
  right: 10px;
  */
  
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  margin-left: -25px;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
