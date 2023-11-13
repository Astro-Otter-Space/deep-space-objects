#!/bin/bash
set -e

#setfacl -R -m u:site:rwX -m u:"$SITE_GID":rwX /var/www/deep-space-objects
#setfacl -dR -m u:site:rwX -m u:"$SITE_GID":rwX /var/www/deep-space-objects

groupadd -g $SITE_GID site
useradd --uid $SITE_UID --gid $SITE_GID --groups $SITE_GID -s /bin/bash site -d /var/www/deep-space-objects
chown -R site:site /var/www/deep-space-objects

exec docker-php-entrypoint "$@"
