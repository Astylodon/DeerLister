#!/bin/sh
set -e
mkdir -p publish
cp -r _internal publish
cp -r vendor publish
cp index.php publish
cp config.toml publish
cp LICENSE publish/_internal
