VERSION="5.2.1"

# isset($_COOKIE["fm_php"]) or exit;
curl -O -L https://github.com/vrana/adminer/releases/download/v${VERSION}/adminer-${VERSION}-en.php

# theme
curl -O -L https://raw.githubusercontent.com/vrana/adminer/refs/heads/master/designs/flat/adminer.css

# plugin
mkdir -p adminer-plugins
cd adminer-plugins

curl -O -L https://raw.githubusercontent.com/vrana/adminer/refs/heads/master/plugins/database-hide.php
curl -O -L https://raw.githubusercontent.com/vrana/adminer-plugins-pematon/refs/heads/master/AdminerCollations.php
curl -O -L https://raw.githubusercontent.com/vrana/adminer-plugins-pematon/refs/heads/master/AdminerJsonPreview.php
