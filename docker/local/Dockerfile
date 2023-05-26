FROM webdevops/php-nginx-dev:8.0-alpine

ENV WEB_DOCUMENT_ROOT=/app/public
ENV PHP_DATE_TIMEZONE=Europe/Bratislava

COPY ./10-php.conf /opt/docker/etc/nginx/vhost.common.d/10-php.conf

WORKDIR /app/

USER application

EXPOSE 80/tcp
