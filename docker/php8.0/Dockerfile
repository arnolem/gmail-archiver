# https://github.com/docker-library/php/issues/1049#issuecomment-673634602

FROM php:8.0-fpm-alpine

RUN apk --no-cache update \
    && apk --no-cache upgrade \
    && apk add --no-cache --virtual \
        .build-deps \
        $PHPIZE_DEPS \
        gcc \
        g++ \
        autoconf \
        tar \
    && apk add --no-cache \
        libxslt-dev \
        tidyhtml-dev \
        net-snmp-dev \
        aspell-dev \
        freetds-dev \
        openldap-dev \
        gettext-dev \
        imap-dev \
        openssh \
        sudo \
        make \
        shadow \
        libmcrypt-dev \
        gmp-dev \
        openssl \
        mariadb-client \
        curl \
        freetype \
        libpng \
        libjpeg-turbo \
        freetype-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        libwebp-dev \
        libzip-dev \
        bzip2-dev \
        postgresql-dev \
        supervisor \
        bash \
        nginx \
        pngquant \
        jpegoptim \
        zip \
        icu-dev \
        libxml2-dev \
        dcron \
        wget \
        rsync \
        ca-certificates \
        oniguruma-dev \
    && phpModules=" \
            bcmath \
            bz2 \
            calendar \
            exif \
            gd \
            gettext \
            gmp \
            imap \
            intl \
            ldap \
            mysqli \
            pcntl \
            pdo_dblib \
            pdo_mysql \
            pdo_pgsql \
            pgsql \
            pspell \
            shmop \
            snmp \
            soap \
            sockets \
            sysvmsg \
            sysvsem \
            sysvshm \
            tidy \
            xsl \
        " \
    && NPROC=$(getconf _NPROCESSORS_ONLN) \
    && docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp \
#    Install APCU
#    @see https://github.com/docker-library/php/issues/1029
    && mkdir -p /usr/src/php/ext/apcu  \
    && mkdir -p /usr/src/php/ext/amqp  \
    && mkdir -p /usr/src/php/ext/igbinary  \
    && mkdir -p /usr/src/php/ext/mongodb  \
    && mkdir -p /usr/src/php/ext/redis  \
    && curl -fsSL https://pecl.php.net/get/apcu | tar xvz -C "/usr/src/php/ext/apcu" --strip 1 \
    && curl -fsSL https://pecl.php.net/get/amqp | tar xvz -C "/usr/src/php/ext/amqp" --strip 1 \
    && curl -fsSL https://pecl.php.net/get/igbinary | tar xvz -C "/usr/src/php/ext/igbinary" --strip 1 \
    && curl -fsSL https://pecl.php.net/get/mongodb | tar xvz -C "/usr/src/php/ext/mongodb" --strip 1 \
    && curl -fsSL https://pecl.php.net/get/redis | tar xvz -C "/usr/src/php/ext/redis" --strip 1 \
    && docker-php-ext-install -j${NPROC} $phpModules \
    && apk del --no-cache gcc g++ freetype-dev libpng-dev libjpeg-turbo-dev .build-deps

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app