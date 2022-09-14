FROM prestashop/prestashop:1.7.7.5-7.1-apache

ENV APP_HOST_PORT=80

RUN echo 'ServerName localhost:${APP_HOST_PORT}' >> /etc/apache2/apache2.conf

RUN chmod -R 777 /var/www/html/modules
