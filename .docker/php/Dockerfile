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
    apt-get install -y libssl-dev && \
    apt-get install -y openssl  && \
    apt-get install -y supervisor && \
    apt-get install -y lsof && \
    docker-php-ext-install zip


###########################################################################
# PHP PDO EXTENSION
###########################################################################

RUN docker-php-ext-install pdo pdo_mysql

###########################################################################
# 安装  composer
###########################################################################

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
RUN printf "no\nyes\nyes\nno\n" | pecl install swoole   && \
    docker-php-ext-enable swoole


###########################################################################
# 配置进程管理
###########################################################################

COPY supervisord.conf /etc/supervisord.conf


#COPY php.sh /home/www/php.sh
#ENTRYPOINT ["./php.sh"]
# Clean up
RUN rm -rf /var/cache/apt/*
WORKDIR "/home/www"

CMD ["php-fpm"]




