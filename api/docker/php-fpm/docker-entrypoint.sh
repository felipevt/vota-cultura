#!/bin/bash
set -e

echo "[ ****************** ] Starting Endpoint of Application [ ****************** ]"

echo "Back - Starting Endpoint of Application"
if ! [ -d "./vendor" ]; then
    #echo " Install depedences whit composer..."
    composer update --ignore-platform-reqs --verbose
fi
exec "$@"

set -- php-fpm

exec "$@"
