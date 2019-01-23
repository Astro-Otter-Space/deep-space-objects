FROM php:7.2-fpm
MAINTAINER HamHamFonFon <balistik.fonfon@gmail.com>
USER root

# Utils
RUN set -eux; \
  apt-get -y update; \
  apt-get -y install curl less vim libicu-dev zlib1g-dev libc-client-dev \
  wget nano libzip-dev pax-utils sudo gnupg unzip libpng-dev

#  && docker-php-ext-install mysqli \
#  && docker-php-ext-install pdo_mysql \
#  && docker-php-ext-install intl \
#  && docker-php-ext-install apcu \
#  && docker-php-ext-install opcache \
#  && docker-php-ext-install zip \
#  && docker-php-ext-install gd

ARG APCU_VERSION=5.1.12
RUN set -eux; \
    docker-php-ext-configure zip --with-libzip; \
    docker-php-ext-install -j$(nproc) intl mbstring pdo pdo_mysql zip; \
    pecl install apcu-${APCU_VERSION}; \
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
    )";

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
&& composer --version

# npm & node
RUN curl -sL https://deb.nodesource.com/setup_9.x | bash
RUN apt-get install -y nodejs npm \
  && update-alternatives --install /usr/bin/node node /usr/bin/nodejs 10
# build tools
RUN apt-get install -y build-essential

# yarn package manager
RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
  && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list

# Git
RUN apt-get install -y git

# bugfix: remove cmdtest to install yarn correctly.
RUN apt-get remove -y cmdtest
RUN apt-get update
RUN apt-get install -y yarn

# Clear archives in apt cache folder
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
