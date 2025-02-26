#!/bin/bash

rm -rf adminer-custom/
git clone https://github.com/pematon/adminer-custom --depth 1
rm -rf adminer-custom/.git

sed -i '1s/.*/<?php isset($_COOKIE["fm_php"]) or exit;/' adminer-custom/adminer.php

cat > adminer-custom/index.php << EOF
<?php

function adminer_object()
{
    include_once "./plugins/plugin.php";

    foreach (glob("plugins/*.php") as $filename) {
        include_once "./$filename";
    }

    if (file_exists("config.php")) {
        include_once "config.php";
    }

    $plugins = [
        new AdminerDatabaseHide(["mysql", "sys", "information_schema", "performance_schema"]),
        new AdminerSimpleMenu(),
        new AdminerCollations(["ascii_general_ci", "utf8mb4_general_ci",  "utf8mb4_vietnamese_ci"]),
        new AdminerTheme('default-green'),
    ];

    return new AdminerPlugin($plugins);
}

require './adminer.php';
EOF

rm -rf db/
mv adminer-custom db
