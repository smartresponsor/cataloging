FROM php:8.2-cli
WORKDIR /app
COPY . /app
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
 && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-interaction --prefer-dist
EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
