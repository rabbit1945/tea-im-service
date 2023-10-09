ARG REDIS_VERSION
FROM redis:${REDIS_VERSION}
EXPOSE 6379
# Clean up
RUN rm -rf /var/cache/apt/*
CMD ["redis-server","--requirepass","123456"]
