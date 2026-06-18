# White LP Factory — PHP generator for Render (Docker)
FROM php:8.3-cli

# Extensions the app needs: zip (ZipArchive), mbstring. curl + openssl are built in.
RUN apt-get update && apt-get install -y --no-install-recommends \
      libzip-dev libonig-dev zip \
    && docker-php-ext-install zip mbstring \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
COPY . /var/www

# writable output dir, and drop the Node runner (not used in the container)
RUN rm -rf /var/www/local-runner/node_modules \
    && mkdir -p /var/www/output \
    && chmod -R 0777 /var/www/output

# Allow a few concurrent requests on PHP's built-in server
ENV PHP_CLI_SERVER_WORKERS=4

# Render injects $PORT; default to 10000 for local docker runs
ENV PORT=10000
EXPOSE 10000
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT} -t /var/www"]
