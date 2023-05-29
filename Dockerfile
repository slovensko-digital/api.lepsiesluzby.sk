FROM webdevops/php-nginx:8.0-alpine

ENV WEB_DOCUMENT_ROOT=/app/public
ENV PHP_DATE_TIMEZONE=Europe/Bratislava

WORKDIR /app/

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY 10-php.conf /opt/docker/etc/nginx/vhost.common.d/10-php.conf
COPY application /app

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress --no-suggest --no-scripts

EXPOSE 80/tcp

CMD ["supervisord"]
