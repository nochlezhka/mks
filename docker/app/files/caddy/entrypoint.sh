#!/bin/sh

set -ue

MODE=${MODE:-http}

case "${MODE}" in
  http)
    ;;
  https)
    : "${DOMAIN:?DOMAIN is required in https mode}"
    : "${EMAIL:?EMAIL is required in https mode}"
    ;;
  *)
    echo "Unknown MODE '${MODE}', expected 'http' or 'https'" >&2
    exit 1
    ;;
esac

exec caddy run --config "/etc/caddy/Caddyfile.${MODE}" --adapter caddyfile
