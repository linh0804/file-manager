# termux file manager install script (curl version)

MANAGER_DIR="$HOME/.local/share/file-manager"
MANAGER_ADDR="localhost:9753"
MANAGER_BIN="$PREFIX/bin/tfm"
MANAGER_FILE="https://static.ngatngay.net/php/file-manager/release.zip"

apt update
apt install -y curl unzip php

mkdir -p "$MANAGER_DIR"
cd "$MANAGER_DIR"

curl -L "$MANAGER_FILE" -o file-manager.zip
unzip -o file-manager.zip
rm -f file-manager.zip

cat << EOF > "$MANAGER_BIN"
exec > /dev/null 2>&1
CURRENT_DIR=\$PWD
cd "$MANAGER_DIR"
PHP_CLI_SERVER_WORKERS=4 nohup php -S "$MANAGER_ADDR" &> /dev/null &
xdg-open "http://$MANAGER_ADDR/index.php?path=\$CURRENT_DIR"
EOF

chmod +x "$MANAGER_BIN"

echo ""
echo "run tfm to start !!"

