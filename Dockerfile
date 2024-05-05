FROM alpine:latest

LABEL Maintainer="Koinz test" \
      Description="Docker image"

# Installing PHP
RUN apk add --no-cache php82 \
    php82-common \
    php82-fpm \
    php82-pdo \
    php82-opcache \
    php82-zip \
    php82-phar \
    php82-iconv \
    php82-cli \
    php82-curl \
    php82-openssl \
    php82-mbstring \
    php82-tokenizer \
    php82-fileinfo \
    php82-json \
    php82-xml \
    php82-xmlwriter \
    php82-simplexml \
    php82-dom \
    php82-pdo_mysql \
    php82-pdo_sqlite \
    php82-tokenizer \
    php82-pecl-redis \
    php82-fpm \
    mysql \
    mysql-client \
    nginx supervisor curl git openssh-client nano bash

RUN php -v

# Set up MySQL configuration
RUN mkdir -p /run/mysqld && \
    chown -R mysql:mysql /run/mysqld && \
    mysql_install_db --user=mysql --ldata=/var/lib/mysql && \
    rm -rf /var/cache/apk/*

# Set up MySQL root password (change 'rootpassword' to your desired password)
ENV MYSQL_ROOT_PASSWORD=rootpassword

# Start MySQL server as a service and create the database
RUN mysqld_safe --user=mysql & \
    sleep 50 && \
    mysql -uroot -p"${MYSQL_ROOT_PASSWORD}" -e "CREATE DATABASE koinz"

COPY database/migrations /migration_files
COPY wait-for-mysql.sh /wait-for-mysql.sh
# Make the script executable
RUN chmod +x /wait-for-mysql.sh

# Configure nginx
COPY .docker/nginx.conf /etc/nginx/nginx.conf

# Configure PHP-FPM
COPY .docker/fpm-pool.conf /etc/php82/php-fpm.d/zzz_custom.conf
COPY .docker/php.ini /etc/php82/conf.d/zzz_custom.ini

# Configure supervisord
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Configure composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Setup Application Folder
RUN mkdir -p /var/www/html
WORKDIR /var/www/html
COPY . .
RUN composer install --no-dev
COPY .env.prod .env
RUN chmod 777 -R storage
RUN chmod 777 -R bootstrap

EXPOSE 80
CMD /bin/sh -c "/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf & \
    sleep 40 && \
    /wait-for-mysql.sh && \
    service mysql start && \
    php artisan migrate && \
    php artisan db:seed"     
    
    