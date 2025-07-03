FROM europe-docker.pkg.dev/yazamos/yazamos-web/php-base as build
WORKDIR /app/
COPY . .
RUN composer require laravel/sanctum
RUN composer install --prefer-dist --optimize-autoloader --no-interaction

FROM php:8.2-apache-buster as production

RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql bcmath
COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
RUN sed -i 's/^post_max_size = .*/post_max_size = 512M/' /usr/local/etc/php/php.ini
RUN sed -i 's/^upload_max_filesize = .*/upload_max_filesize = 60M/' /usr/local/etc/php/php.ini
RUN sed -i 's/^memory_limit = .*/memory_limit = 512M/' /usr/local/etc/php/php.ini
RUN sed -i 's/^short_open_tag = .*/short_open_tag = On/' /usr/local/etc/php/php.ini
RUN sed -i 's/^max_execution_time = .*/max_execution_time = 3600/' /usr/local/etc/php/php.ini
RUN sed -i 's/^gc_maxlifetime = .*/gc_maxlifetime = 3600/' /usr/local/etc/php/php.ini
RUN cat /usr/local/etc/php/php.ini

COPY --from=build /app /var/www/html
COPY .env.example /var/www/html/.env
RUN ls -l /var/www/html/public
ARG DB_PASSWORD
EXPOSE 80

ENV PORT=80
ENV APP_ENV=production
ENV VUE_APP_URL="https://stage.yazamos.com/"
ENV APP_URL="https://stage.yazamos.com"
ENV DB_CONNECTION=mysql

ENV DB_HOST=10.1.0.3
ENV DB_PORT=3306
ENV DB_DATABASE=yazamos
ENV DB_USERNAME=yazamos
ENV DB_PASSWORD=Ch2}bho3XYY3g44s

ENV MAIL_MAILER=smtp
ENV MAIL_HOST=smtp.gmail.com
ENV MAIL_PORT=587
ENV MAIL_USERNAME=dhavalkapupara7@gmail.com
ENV MAIL_PASSWORD=psqmkwqumwvdtfnc
ENV MAIL_ENCRYPTION=tls
ENV MAIL_FROM_ADDRESS="dhavalkapupara7@gmail.com"
ENV MAIL_FROM_NAME=""

RUN php artisan key:generate --no-interaction


ENV TRANZILA_SUPPLIER=testchkr
ENV TRANZILA_TERMINAL=testchkr
ENV TRANZILA_PASSWORD=Jgub^%352
ENV TRANZILA_CURRENCY=ILS

RUN php artisan migrate --force
RUN php artisan queue:restart

RUN apt-get update && apt-get install -y supervisor
# Copy Supervisor configuration file
COPY supervisor.conf /etc/supervisor/conf.d/supervisor.conf


COPY entrypoint.sh /usr/local/bin/entrypoint.sh
ENTRYPOINT ["sh", "/usr/local/bin/entrypoint.sh"]
