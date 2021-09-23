# From https://github.com/dunglas/symfony-docker/blob/8efe315479755076cfc6ad737ce5300486d9a9f8/Dockerfile
###################
# Node
###################
#ARG NODE_VERSION=14
#FROM node:${NODE_VERSION}-alpine AS symfony_assets_builder
#WORKDIR /var/www/deep-space-objects

###################
# PHP
##################
ARG PHP_VERSION=7.4

# "php" stage
FROM php:${PHP_VERSION}-fpm-alpine3.13 AS symfony_php

RUN apk add --no-cache \
        acl \
        fcgi \
        file \
        gettext \
        git \
        jq \
    ;

ARG APCU_VERSION=5.1.19
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
	    $PHPIZE_DEPS \
    	curl-dev \
	    icu-dev \
    	libmemcached-dev \
	    libzip-dev \
	    zlib-dev \
    	# libonig-dev \ If need to install mbstring
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
    	curl \
    	json \
    	pdo \
    	pdo_mysql \
	    intl \
	    zip \
	; \
	pecl install \
	    apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
	    apcu \
	    opcache \
	; \
	\
	runDeps="$( \
	    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
	        | tr ',' '\n' \
	        | sort -u \
	        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY conf.d/symfony.dev.ini $PHP_INI_DIR/conf.d/symfony.ini

RUN set -eux; \
	{ \
		echo '[www]'; \
		echo 'ping.path = /ping'; \
	} | tee /usr/local/etc/php-fpm.d/docker-healthcheck.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /srv/app

COPY . .

#RUN set -eux; \
#	mkdir -p var/cache var/log; \
#	composer install --prefer-dist --no-scripts --no-interaction; \
#	composer dump-autoload --classmap-authoritative ; \
#	composer symfony:dump-env prod; \
#	composer run-script post-install-cmd;
#	chmod +x bin/console; sync
VOLUME /srv/app/var

COPY docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint


ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

WORKDIR /srv/app
#COPY --from=symfony_php /var/www/deep-space-objects/public public/
#COPY --from=symfony_assets_builder /var/www/deep-space-objects/public/build public/build
