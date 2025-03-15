FROM composer:latest AS composer-stage

COPY composer.json composer.lock ./

CMD ["composer", "install"] 

FROM php:latest
WORKDIR /var/www/html
COPY . .

# RUN docker-php-ext-install -v $(php -i | grep 'extension_dir' | awk '{print $NF}') pdo pdo_mysql

EXPOSE 5151

CMD ["php", "bin/console", "server:run", "-d", "/var/www/html", "--port", "5151"]
