FROM nginx
MAINTAINER Martijn Endenburg <martijn.endenburg@gmail.com>

RUN apt-get update && \
    apt-get install -y \
        php5-curl \
        php5-fpm \
        php5-mongo

ADD librarian.tar.gz /opt/webapp
COPY nginx.conf /etc/nginx/nginx.conf

RUN chown -R www-data:www-data /opt/webapp

CMD service php5-fpm start && nginx -g 'daemon off;'
