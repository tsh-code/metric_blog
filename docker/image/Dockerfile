FROM php:7.4-fpm

RUN apt-get update

RUN usermod -u 1000 www-data

RUN apt-get install -y zlib1g-dev libpq-dev git libicu-dev libxml2-dev libzip-dev \
    && pecl install -o -f redis \
    && rm -rf /tmp/pear \
    && docker-php-ext-configure intl \
    && docker-php-ext-enable redis \
    && docker-php-ext-install intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-install zip xml \
    && apt-get -y install nginx gettext

RUN curl --insecure https://getcomposer.org/download/1.10.1/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Berlin /etc/localtime
RUN "date"

ADD ./docker/image/etc/nginx/nginx.conf /etc/nginx/
ADD ./docker/image/etc/nginx/symfony.conf /etc/nginx/sites-available/symfony.conf.template

RUN rm /etc/nginx/sites-enabled/default

ADD ./docker/image/bin/entrypoint.bash /usr/local/bin/entrypoint
RUN chmod a+x /usr/local/bin/entrypoint


WORKDIR /var/www/project

ENTRYPOINT ["entrypoint"]
CMD ["--start-http-server"]

ENV APP_CGI_SERVER_HOST="php-fpm" \
  APP_CGI_SERVER_PORT="9000" \
  APP_UPDATE_DATABASE="on"

COPY ./ /var/www/project
RUN composer install

RUN chmod -R a+r /var/www/project \
&& chmod -R a+x /var/www/project/bin/* \
&& mkdir -p /var/www/project/var/tmp \
&& mkdir -p /var/www/project/var/cache/prod \
&& chmod -R a+w /var/www/project/var \
&& find /var/www/project/var -type d -exec chmod a+x {} +
