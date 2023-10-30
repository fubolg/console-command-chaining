FROM php:8.0-fpm
LABEL maintainer="vlad@ecomitize.com"

RUN mkdir ~/.ssh

RUN buildDeps=" \
        default-libmysqlclient-dev \
        libbz2-dev \
        libmemcached-dev \
        libsasl2-dev \
    " \
    runtimeDeps=" \
        curl \
        git \
        zip \
        unzip \
        vim \
        wget \
        libfreetype6-dev \
        libzip-dev \
        libicu-dev \
        libjpeg-dev \
        libmemcachedutil2 \
        libpng-dev \
        libonig-dev \
        libpq-dev \
        libxml2-dev \
        libevent-dev \
        librabbitmq-dev \
        libcurl4-openssl-dev \
        pkg-config \
        libssl-dev \
        openssh-client \
    " \
    && apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y $buildDeps $runtimeDeps \
    && docker-php-ext-install bcmath bz2 curl calendar iconv intl mbstring opcache pdo_mysql soap zip \
    && docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/  \
    && docker-php-ext-install pcntl gd exif\
    && docker-php-ext-enable opcache \
    && pecl install memcached redis \
    && docker-php-ext-enable memcached.so redis.so \
    && apt-get purge -y --auto-remove $buildDeps \
    && apt-get install -y procps \
    && rm -r /var/lib/apt/lists/*

RUN ln -sf /dev/stdout /var/log/access.log && ln -sf /dev/stderr /var/log/error.log

#RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis

RUN set -xe \
    && pecl install \
        redis \
        amqp \
        mongodb \
    && rm -rf /tmp/pear \
    && docker-php-ext-enable \
        redis \
        amqp \
        mongodb

RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends
RUN pecl install imagick && docker-php-ext-enable imagick

RUN wget http://pecl.php.net/get/amqp-1.11.0 -O amqp.tar.gz \
    && tar -zxvf amqp.tar.gz \
    && cd amqp-1.11.0 \
    && phpize \
    && ./configure --with-amqp \
    && make \
    && make install

## Install Xdebug
#RUN echo "Install xdebug by pecl"
#RUN yes | pecl install xdebug-2.9.8 \
#    && docker-php-ext-enable xdebug \
#    && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.remote_enable=on\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.remote_autostart=on\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.default_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.remote_handler=dbgp\n" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.remote_port=9201" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.remote_host=localhost" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.idekey=HELIX" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
#
#RUN export PHP_IDE_CONFIG="serverName=localhost"

## Now we copy will copy very simple php.ini file and change the timezone by ENV variable
COPY php.ini /usr/local/etc/php/

## Install Deployer
ENV DEPLOYER_VERSION=6.8.0
RUN curl -L https://deployer.org/releases/v$DEPLOYER_VERSION/deployer.phar > /usr/local/bin/dep \
    && chmod +x /usr/local/bin/dep

## Install Composer
RUN echo "Install composer globally"
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && ln -s $(composer config --global home) /root/composer
RUN composer self-update --2
ENV PATH=$PATH:/root/composer/vendor/bin COMPOSER_ALLOW_SUPERUSER=1


## Setup TMP folder
RUN mkdir /tmp/helix
RUN rm -rf /tmp/helix/*
RUN chmod 777 /tmp/helix -R
RUN mkdir /.cache -m 777

#USER www-data

CMD ["php-fpm"]