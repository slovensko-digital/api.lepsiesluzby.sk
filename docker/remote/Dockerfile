FROM webdevops/php-nginx:8.0-alpine

ENV WEB_DOCUMENT_ROOT=/app/public
ENV PHP_DATE_TIMEZONE=Europe/Bratislava

COPY ./docker/remote/10-php.conf /opt/docker/etc/nginx/vhost.common.d/10-php.conf

WORKDIR /app/

#USER application
COPY ./application /app
COPY ./docker/run.sh /app/run.sh
RUN chmod 755 /app/run.sh

EXPOSE 80/tcp

HEALTHCHECK CMD curl -f http://localhost || exit 1

CMD ["/app/run.sh"]
