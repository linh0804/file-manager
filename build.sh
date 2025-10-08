rm -f release.zip
zip -r -9 release.zip . \
  -x ".git/*" \
  -x ".github/*" \
  -x "node_modules/*" \
  -x ".config.php" \
  -x "config.inc.php" \
  -x "tmp/*" \
  -x "build.sh" \
  -x "install_termux.sh" \
  -x "package.json" \
  -x "pnpm-lock.yaml" \
  -x "composer.phar" \
  -x "composer.json" \
  -x "composer.lock"
