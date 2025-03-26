# termux file manager install script

MANAGER_DIR="$HOME/.local/share/file-manager"
MANAGER_ADDR="localhost:9753"
MANAGER_BIN="$PREFIX/bin/tfm"
MANAGER_FILE="https://github.com/ngatngay/file-manager/releases/latest/download/file-manager.zip"

apt update
apt install -y wget unzip php

mkdir -p $MANAGER_DIR
cd $MANAGER_DIR

wget $MANAGER_FILE -O file-manager.zip
unzip -o file-manager.zip
rm -f file-manager.zip

cat << EOF > $MANAGER_BIN
exec > /dev/null 2>&1
CURRENT_DIR=\$PWD
cd $MANAGER_DIR
PHP_CLI_SERVER_WORKERS=4 nohup php -S $MANAGER_ADDR &> /dev/null &
xdg-open "http://$MANAGER_ADDR/index.php?dir=\$CURRENT_DIR"
EOF

chmod +x $MANAGER_BIN

echo ""
echo "run tfm to start !!"
