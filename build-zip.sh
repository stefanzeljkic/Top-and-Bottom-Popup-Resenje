#!/usr/bin/env bash
# Builds dist/resenje-top-bottom-popup.zip (the file uploaded to WordPress.org / attached to releases).
set -euo pipefail
cd "$(dirname "$0")"
slug=resenje-top-bottom-popup
rm -rf dist && mkdir -p "dist/$slug"
cp -r "$slug.php" uninstall.php readme.txt LICENSE assets "dist/$slug/"
( cd dist && /c/Windows/System32/tar.exe -a -cf "$slug.zip" "$slug" )
echo "dist/$slug.zip"
