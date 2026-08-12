#!/bin/bash
set -eu

source ./tests/secrets.sh
source ./_myapps-common/test/export_secrets.sh ${SECRETS[*]}

# Launch function
export FUNCTION_TARGET=main_event
export FUNCTION_SIGNATURE_TYPE=cloudevent
APP_ENV=local php -S localhost:8081 vendor/bin/router.php

source ./_myapps-common/test/unset_secrets.sh ${SECRETS[*]}
