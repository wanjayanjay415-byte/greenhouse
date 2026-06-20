#!/bin/bash
set -e

PORT="${PORT:-8080}"

echo "=== GreenHouse Container Starting ==="
echo "PORT=${PORT}"

echo "Listen ${PORT}" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/000-default.conf 2>/dev/null || true

echo "=== Apache config test ==="
apache2ctl configtest 2>&1 || {
    echo "=== APACHE CONFIG INVALID ==="
    cat /etc/apache2/ports.conf
    cat /etc/apache2/sites-available/000-default.conf
    exit 1
}

echo "=== Starting Apache on port ${PORT} ==="
exec apache2-foreground
