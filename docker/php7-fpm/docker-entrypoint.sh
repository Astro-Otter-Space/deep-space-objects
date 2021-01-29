#!/bin/bash
set -e

setfacl -R -m u:www-data:rwX -m u:"$SITE_GID":rwX var
setfacl -dR -m u:www-data:rwX -m u:"$SITE_GID":rwX var

exec docker-php-entrypoint "$@"
