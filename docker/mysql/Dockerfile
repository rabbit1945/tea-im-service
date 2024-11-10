ARG MYSQL_VERSION
FROM migs/mysql-${MYSQL_VERSION}

RUN  chmod -Rf 777 /var/log && mkdir -p /var/log/mysql
RUN  echo "" >> /var/log/mysql/error.log
RUN  chown -R root:root  /var/log/mysql && chmod -Rf 777 /var/log/mysql/error.log
# Clean up
RUN rm -rf /var/cache/apt/*
CMD ["mysqld"]

EXPOSE 3306
