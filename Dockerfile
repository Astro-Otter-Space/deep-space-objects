#syntax=docker/dockerfile:1.4
ARG PHP_VERSION=8.1
ARG NODE_VERSION=14

#######################
# Node
#######################
FROM node:${NODE_VERSION}-alpine AS app_node

WORKDIR /var/www/deep-space-objects
#RUN mkdir public

COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# The different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target

# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact

#######################
# PHP
#######################
FROM php:${PHP_VERSION}-fpm-alpine AS app_php

ENV APP_ENV=dev
WORKDIR /var/www/deep-space-objects

COPY --from=mlocati/php-extension-installer --link /usr/bin/install-php-extensions /usr/local/bin/

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		fcgi \
		file \
		gettext \
		git \
        libsodium-dev \
        curl \
        openssl \
        apt-transport-https \
	;

RUN set -eux; \
    install-php-extensions \
    	intl \
    	zip \
    	apcu \
		opcache \
        pdo \
        pdo_mysql \
        gd \
        sodium \
    ;

###> recipes ###
###< recipes ###

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY --link docker/php/conf.d/app.ini $PHP_INI_DIR/conf.d/
COPY --link docker/php/conf.d/app.prod.ini $PHP_INI_DIR/conf.d/

COPY --link docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
RUN mkdir -p /var/run/php

COPY --link docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY --link docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer:2 --link /usr/bin/composer /usr/bin/composer

# prevent the reinstallation of vendors at every changes in the source code
COPY composer.* symfony.* ./
RUN set -eux; \
    if [ -f composer.json ]; then \
        composer config --json extra.symfony.docker 'true'; \
		composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress; \
		composer clear-cache; \
    fi

# copy sources
COPY --link  . .
#RUN rm -Rf docker/

RUN set -eux; \
	mkdir -p var/cache var/log; \
    if [ -f composer.json ]; then \
		composer dump-autoload --classmap-authoritative --no-dev; \
		composer dump-env prod; \
		composer run-script --no-dev post-install-cmd; \
		chmod +x bin/console; sync; \
    fi

COPY --from=app_php --link /var/www/deep-space-objects/public public/

#######################
# Nginx
#######################
FROM debian:stretch as app_nginx
ARG NGINX_HOST
ARG UID

# Install nginx
RUN apt-get update && apt-get install -y nginx wget

# Instal certbot for SSL
#RUN apt-get install certbot python-certbot-nginx -t stretch-backports

# Configure Nginx
ADD nginx.conf /etc/nginx/

ADD symfony.conf /etc/nginx/sites-available/
#RUN envsubst "${NGINX_HOST}" < /etc/nginx/sites-available/default.template > /etc/nginx/sites-available/symfony.conf && nginx -g 'daemon off;'
RUN sed "/server_name nginx_host;/c\    server_name ${NGINX_HOST};" -i /etc/nginx/sites-available/symfony.conf
RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf

# Configure the virtual host
RUN ln -s /etc/nginx/sites-available/symfony.conf /etc/nginx/sites-enabled/symfony
RUN rm /etc/nginx/sites-enabled/default

# Add certificate SSL
#RUN certbot --nginx certonly
RUN usermod -u ${UID} www-data

# Run Nginx
CMD ["nginx"]

# Expose ports
EXPOSE 80
EXPOSE 443
