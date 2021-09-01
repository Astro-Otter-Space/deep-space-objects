# From https://github.com/dunglas/symfony-docker/blob/8efe315479755076cfc6ad737ce5300486d9a9f8/Dockerfile

ARG PHP_VERSION=7.4

#ENV PHP_EXTENTIONS \
#   intl \
#   zip

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
COPY docker/php7-fpm/conf.d/symfony.dev.ini $PHP_INI_DIR/conf.d/symfony.ini

RUN set -eux; \
	{ \
		echo '[www]'; \
		echo 'ping.path = /ping'; \
	} | tee /usr/local/etc/php-fpm.d/docker-healthcheck.conf

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /var/www/deep-space-objects

COPY . .

RUN set -eux; \
	mkdir -p var/cache var/log; \
#	composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
#	composer dump-autoload --classmap-authoritative --no-dev; \
#	composer symfony:dump-env dev; \
#	composer run-script --no-dev post-install-cmd; sync
VOLUME /var/www/deep-space-objects/var

COPY docker/php7-fpm/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]
COPY docker/php7-fpm/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

# NPM & Node
RUN curl -sL https://deb.nodesource.com/setup_9.x | bash
RUN apt-get install -y nodejs npm \
  && update-alternatives --install /usr/bin/node node /usr/bin/nodejs 10

# build tools
RUN apt-get install -y build-essential

# yarn package manager
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
  && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

# bugfix: remove cmdtest to install yarn correctly.
RUN apt-get remove -y cmdtest
RUN apt-get update
RUN apt-get install -y yarn

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

COPY --from=symfony_php /var/www/deep-space-objects/public public/
