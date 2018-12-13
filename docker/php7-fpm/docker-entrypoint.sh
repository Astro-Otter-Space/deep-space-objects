#!/bin/bash

groupadd -g $SITE_GID site
useradd --uid $SITE_UID --gid $SITE_GID --groups $SITE_GID -s /bin/bash site -d /home/site

mkdir /home/site/.config
chown -R site:site /home/site

php-fpm -F
