ARG MYSQL_VERSION
FROM mysql:${MYSQL_VERSION}

RUN mkdir -p /var/log/mysql
RUN  echo "" >> /var/log/mysql/error.log
RUN chown -R mysql:root  /var/log/mysql && chmod -Rf 777 /var/log/mysql/error.log
# Clean up
RUN rm -rf /var/cache/apt/*
CMD ["mysqld"]

EXPOSE 3306
