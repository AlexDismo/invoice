#!/bin/bash
set -e

cd /app

if [ ! -d node_modules/.bin ]; then
  npm install
fi

exec "$@"
