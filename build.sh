rm -f release.zip

zip -r -9 release.zip . \
  -x ".*" \
  -x "*/.*" \
  -x "node_modules/*" \
  -x "build.sh" \
  -x "build_local.sh" \
  -x "package.json" \
  -x "pnpm-lock.yaml" \
  -x "composer.phar" \
  -x "composer.json" \
  -x "composer.lock"
