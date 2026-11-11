#!/bin/bash
set -eu

source ./tests/secrets.sh
if [ -d "./_myapps-common" ]; then
    source ./_myapps-common/test/export_secrets.sh ${SECRETS[*]}
fi

# Launch function
export FUNCTION_TARGET=main_http
APP_ENV=local php -S localhost:${PORT:-8080} vendor/bin/router.php

if [ -d "./_myapps-common" ]; then
    source ./_myapps-common/test/unset_secrets.sh ${SECRETS[*]}
fi
