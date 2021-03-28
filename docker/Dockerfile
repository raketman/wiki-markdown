FROM php:7.4-cli-alpine3.13

RUN mkdir -p /var/lib/meilisearch
RUN curl -L -o /var/lib/meilisearch  https://install.meilisearch.com | sh

WORKDIR /var/www/app

#RUN git pull

CMD ["php bin/server run"]
