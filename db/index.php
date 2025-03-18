<?php

function adminer_object()
{
    require_once "./plugins/plugin.php";

    foreach (glob("plugins/*.php") as $filename) {
        require_once "./$filename";
    }

    if (file_exists("config.php")) {
        require_once "config.php";
    }

    $plugins = [
        new AdminerDatabaseHide(["mysql", "sys", "information_schema", "performance_schema"]),
        new AdminerSimpleMenu(),
        new AdminerCollations(["utf8mb4_general_ci",  "utf8mb4_vietnamese_ci"]),
        new AdminerTheme('default-green'),
    ];

    return new AdminerPlugin($plugins);
}

require_once './adminer.php';
