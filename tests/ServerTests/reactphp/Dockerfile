FROM php:8.2

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update && apt-get -y upgrade \
    && apt-get -y --no-install-recommends install apt-utils unzip git

RUN chmod uga+x /usr/local/bin/install-php-extensions && sync && install-php-extensions zip pcntl

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/bin/composer

EXPOSE 80
ENTRYPOINT ["/usr/local/bin/php"]
CMD ["cli", "server:run", "--server", "ReactPHP", "-vvv"]