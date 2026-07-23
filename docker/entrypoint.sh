#!/bin/bash
set -e

php-fpm -F &
PHP_FPM_PID=$!

caddy run --config /etc/caddy/Caddyfile --adapter caddyfile &
CADDY_PID=$!

trap 'kill -TERM $PHP_FPM_PID $CADDY_PID 2>/dev/null' TERM INT

wait -n "$PHP_FPM_PID" "$CADDY_PID"
exit $?
