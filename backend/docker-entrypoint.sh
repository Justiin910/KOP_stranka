#!/bin/sh
set -e

if [ -f /var/www/html/artisan ]; then
  echo "Ensuring Laravel storage symlink exists..."
  php artisan storage:link --force || echo "Warning: storage:link failed; continuing startup."
fi

exec "$@"