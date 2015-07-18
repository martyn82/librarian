FROM nginx
MAINTAINER Martijn Endenburg <martijn.endenburg@gmail.com>

RUN apt-get update && \
    apt-get install -y \
        php5-fpm

ADD librarian.tar.gz /opt/www/webapp
COPY nginx.conf /etc/nginx/nginx.conf

RUN chown -R www-data:www-data /opt/www/webapp

CMD service php5-fpm start && nginx -g 'daemon off;'
