<?php

function adminer_object()
{
    // Required to run any plugin.
    include_once "./plugins/plugin.php";

    // Plugins auto-loader.
    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }

    if (file_exists("config.php")) {
        include_once "config.php";
    }

    // Specify enabled plugins here.
    $plugins = [
        new AdminerDatabaseHide(["mysql", "sys", "information_schema", "performance_schema"]),
        new AdminerSimpleMenu(),
        new AdminerCollations(["ascii_general_ci", "utf8mb4_general_ci",  "utf8mb4_vietnamese_ci"]),
        new AdminerTheme('default-green'),
    ];

    return new AdminerPlugin($plugins);
}

// Include original Adminer or Adminer Editor.
include "./adminer.php";
