ARG MYSQL_VERSION
FROM mysql:${MYSQL_VERSION}

RUN mkdir /var/log/mysql

RUN chmod 777 /var/log/mysql && chown -R mysql:root  /var/log/mysql && chmod 644 /var/log/mysql

CMD ["mysqld"]

EXPOSE 3306
