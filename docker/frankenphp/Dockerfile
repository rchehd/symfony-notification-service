FROM dunglas/frankenphp:latest

# Install needed extensions.
RUN apt-get update && apt-get install -y \
        librabbitmq-dev \
    && install-php-extensions \
        pdo_pgsql \
        zip \
        intl \
        opcache \
        apcu \
        redis \
        amqp \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy the project code.
COPY --chown=frankenphp:frankenphp . /app

# Install Xdebug, if variable XDEBUG=1
ARG XDEBUG
RUN if [ "$XDEBUG" = "1" ]; then \
    install-php-extensions xdebug; \
    echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi

# Install dependencies via Composer.
RUN composer install --no-interaction --optimize-autoloader --no-scripts

RUN mkdir -p var/log

# Grant access for frankenphp user.
RUN chmod -R 777 var/cache var/log
