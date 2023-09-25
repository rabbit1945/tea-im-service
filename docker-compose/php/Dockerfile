FROM php:8.0.3-fpm


ARG CHANGE_SOURCE=false
RUN if [ ${CHANGE_SOURCE} = true ]; then \
    # Change application source from deb.debian.org to aliyun source
    sed -i 's/deb.debian.org/mirrors.tuna.tsinghua.edu.cn/' /etc/apt/sources.list && \
    sed -i 's/security.debian.org/mirrors.tuna.tsinghua.edu.cn/' /etc/apt/sources.list && \
    sed -i 's/security-cdn.debian.org/mirrors.tuna.tsinghua.edu.cn/' /etc/apt/sources.list \
;fi
###########################################################################
# PHP ZIP EXTENSION
###########################################################################
RUN apt-get update && \
    apt-get install ca-certificates && \
    apt-get install -y zip unzip && \
    apt-get install -y zlib1g-dev && \
    apt-get install -y libzip-dev && \
    docker-php-ext-install zip


###########################################################################
# PHP PDO EXTENSION
###########################################################################

RUN docker-php-ext-install pdo pdo_mysql



RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer  && \
    composer config -g repo.packagist composer https://packagist.phpcomposer.com



###########################################################################
# PHP REDIS EXTENSION
###########################################################################
RUN pecl install redis \
    && docker-php-ext-enable redis

###########################################################################
# PHP SWOOLE EXTENSION
###########################################################################
RUN pecl install swoole  && \
    docker-php-ext-enable swoole




#COPY php.sh /home/www/php.sh
#ENTRYPOINT ["./php.sh"]

WORKDIR "/home/www"

CMD ["php-fpm"]




