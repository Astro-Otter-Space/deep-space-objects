#!/bin/bash

groupadd -g $SITE_GID site
useradd -u $SITE_UID -g $SITE_GID -G $SITE_GID -s /bin/bash site -d /home/sitee/site
mkdir /home/site/.config
chown -R site:site /home/site

php-fpm -F
