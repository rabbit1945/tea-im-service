FROM php:8.0.3-fpm
ENV user www


#RUN usermod -u 1000 www-data && usermod -G staff www-data
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
    apt-get install -y zip unzip git && \
    apt-get install -y zlib1g-dev && \
    apt-get install -y libzip-dev && \
    apt-get install -y libssl-dev && \
    apt-get install -y libwebp-dev libjpeg-dev libpng-dev libfreetype6-dev  libgd3 && \
    apt-get install -y openssl  && \
    apt-get install -y supervisor && \
    apt-get install -y lsof && \
    docker-php-ext-install zip && \
    apt-get install sudo

RUN groupadd docker
RUN useradd -m -d /home/${user} ${user} && \
    chown -R ${user} /home/${user} && \
    adduser ${user} sudo && \
    echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers


RUN usermod -a -G docker ${user}




RUN  cd /usr/local/etc/php/ && \
     cp php.ini-development php.ini && \
     ln -s php.ini /etc/php.ini


###########################################################################
# PHP GD EXTENSION
###########################################################################

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
      && docker-php-ext-install gd \
      && docker-php-ext-enable gd


###########################################################################
# PHP exif EXTENSION
###########################################################################

RUN docker-php-ext-install exif \
    && docker-php-ext-enable exif

###########################################################################
# PHP PDO EXTENSION
###########################################################################

RUN  docker-php-ext-install pdo pdo_mysql

###########################################################################
# 安装  composer
###########################################################################

RUN curl -sS https://install.phpcomposer.com/installer | php \
    && mv composer.phar /usr/local/bin/composer  && \
    composer config -g repos.packagist composer https://mirrors.aliyun.com/composer/



###########################################################################
# PHP REDIS EXTENSION
###########################################################################
RUN pecl install redis \
    && docker-php-ext-enable redis

###########################################################################
# PHP SWOOLE EXTENSION
###########################################################################
RUN  printf "no\nyes\nyes\nno\n" | pecl install https://pecl.php.net/get/swoole-5.1.1.tgz && \
     docker-php-ext-enable swoole

###########################################################################
# PHP sockets EXTENSION
###########################################################################
RUN docker-php-ext-install sockets

###########################################################################
# PHP amqp EXTENSION
###########################################################################
RUN apt-get update && apt-get install -y \
    librabbitmq-dev \
    && pecl install amqp \
    && docker-php-ext-enable amqp

###########################################################################
# 配置进程管理
###########################################################################

COPY supervisord.conf /etc/supervisord.conf
RUN  rm -rf /var/cache/apt/*




RUN sudo chmod -Rf 777 /var/log/supervisor
WORKDIR "/home/${user}"



CMD ["php-fpm"]




