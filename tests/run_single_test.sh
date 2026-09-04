#!/bin/bash
set -eu

source ./tests/secrets.sh
if [ -d "./_myapps-common" ]; then
    source ./_myapps-common/test/export_secrets.sh ${SECRETS[*]}
fi

# run tests
bash ./tests/run_linter.sh

echo "Running PHPUnit of $1..."
./vendor/bin/phpunit --testdox $1

if [ -d "./_myapps-common" ]; then
    source ./_myapps-common/test/unset_secrets.sh ${SECRETS[*]}
fi
