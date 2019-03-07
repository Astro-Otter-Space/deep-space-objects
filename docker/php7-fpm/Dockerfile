# Source : https://framagit.org/nicolasunivlr/docker-symfony4/blob/master/php7-fpm/Dockerfile
FROM php:7.2-fpm
#USER root
ARG TIMEZONE
ARG UID

MAINTAINER HamHamFonFon <balistik.fonfon@gmail.com>

# Utils
RUN set -eux; \
  apt-get update; \
  apt-get -y install curl less vim libicu-dev libmemcached-dev zlib1g-dev libc-client-dev \
  openssl wget nano libzip-dev pax-utils sudo gnupg unzip libpng-dev

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version \
    && chmod 755 /usr/local/bin/composer

# Change uid and gid nginx user
RUN usermod -u ${UID} www-data \
    && groupmod -g ${UID} www-data

# Create directeories and add rights
RUN mkdir -p /var/www/.composer \
    && mkdir -p /var/www/.npm \
    && chown -R www-data:www-data /var/www

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini
RUN "date"

# Install
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-configure zip --with-libzip
RUN docker-php-ext-install zip

# APCU
RUN pecl install apcu \
    && docker-php-ext-enable apcu

# OPCACHE
RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

# memcached
RUN pecl install memcached-3.0.4 \
    && docker-php-ext-enable memcached

RUN { \
		echo 'opcache.enable= 1'; \
		echo 'opcache.enable_cli=1'; \
		# Symfony requirements
		echo 'opcache.memory_consumption=256'; \
        echo 'opcache.max_wasted_percentage=10'; \
		echo 'opcache.validate_timestamps=0'; \
		# Optimize
		echo 'opcache.max_accelerated_files=10000'; \
		echo 'opcache.interned_strings_buffer=8'; \
		echo 'opcache.save_comments=1'; \
		echo 'opcache.revalidate_freq=1'; \
		#echo 'opcache.fast_shutdown=1'; \
	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Others
RUN docker-php-ext-install -j$(nproc) intl mbstring ; \
    \
    runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
            | tr ',' '\n' \
            | sort -u \
            | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
    )";

# Conf PHP
RUN { \
		echo 'realpath_cache_size=4096k'; \
		echo 'realpath_cache_ttl=7200'; \
    } > /usr/local/etc/php/conf.d/php.ini

# Install XDebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# Errors

RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "log_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "error_log = /var/log/php/error.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
# XDEBUG
RUN echo "xdebug.remote_enable=0" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.max_nesting_level=500" >> /usr/local/etc/php/conf.d/030-custom.ini
# Custom options
RUN echo "short_open_tag = Off" >> /usr/local/etc/php/conf.d/030-custom.ini
RUN echo "memory_limit= 512M" >> /usr/local/etc/php/conf.d/030-custom.ini

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

# Clear archives in apt cache folder
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Scalingo
RUN curl -O https://cli-dl.scalingo.io/install && bash install

CMD ["php-fpm", "-F"]
EXPOSE 9000

#COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
#RUN chmod +x /usr/local/bin/docker-entrypoint
#ENTRYPOINT ["docker-entrypoint"]
