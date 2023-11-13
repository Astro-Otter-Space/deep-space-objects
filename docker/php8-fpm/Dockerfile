ARG PHP_VERSION=8.2
ARG TIMEZONE

FROM php:${PHP_VERSION}-fpm
MAINTAINER HamHamFonFon <balistik.fonfon@gmail.com>
# Utils
RUN apt-get update && \
    apt-get install -y \
    acl \
    curl \
    less \
    vim \
    git \
    libicu-dev \
    libmemcached-dev \
    zlib1g-dev \
    libc-client-dev \
    openssl \
    wget \
    nano \
    libzip-dev \
    zip \
    pax-utils \
    sudo \
    gnupg \
    unzip \
    libpng-dev \
    libsodium-dev \
    ca-certificates \
    apt-transport-https

# Install
RUN docker-php-ext-configure intl
RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install intl
RUN docker-php-ext-install zip
RUN docker-php-ext-install gd
RUN docker-php-ext-install sodium

# OPCACHE
RUN docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

# APCU
RUN pecl install apcu && docker-php-ext-enable apcu

# Create a group with GID 1000
RUN groupadd -g 1000 sitegroup

# Create a user named "site" with UID 1000 and GID 1000
RUN useradd -ms /bin/bash --uid 1000 --gid 1000 site
USER site


# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create directeories and add rights
RUN mkdir -p /var/www/.composer && \
    mkdir -p /var/www/.npm
#    && chown -R www-data:www-data /var/www

# Set timezone
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && echo ${TIMEZONE} > /etc/timezone
RUN printf '[PHP]\ndate.timezone = "%s"\n', ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini
RUN "date"

# memcached
#RUN pecl install memcached-3.0.4 \
#    && docker-php-ext-enable memcached
RUN set -ex \
    && rm -rf /var/lib/apt/lists/* \
    && MEMCACHED="`mktemp -d`" \
    && curl -skL https://github.com/php-memcached-dev/php-memcached/archive/master.tar.gz | tar zxf - --strip-components 1 -C $MEMCACHED \
    && docker-php-ext-configure $MEMCACHED \
    && docker-php-ext-install $MEMCACHED \
    && rm -rf $MEMCACHED

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
RUN docker-php-ext-install -j$(nproc) mbstring ; \
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
#RUN pecl install xdebug
#RUN docker-php-ext-enable xdebug

# Errors
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "log_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "error_log = /var/log/php/error.log" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Custom options
RUN echo "short_open_tag = Off" >> /usr/local/etc/php/conf.d/030-custom.ini
RUN echo "memory_limit= 2G" >> /usr/local/etc/php/conf.d/030-custom.ini

# NPM & Node
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get install -y nodejs
RUN npm install -g yarn

# Clear archives in apt cache folder
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy existing application directory permissions
COPY --chown=site:site . /var/www/deep-space-objects

EXPOSE 9000
CMD ["php-fpm"]
