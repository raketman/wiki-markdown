#FROM php:7.4-cli-alpine3.13
#FROM alpine:latest
#FROM centos:8
#FROM php:7.4.16-cli-buster
FROM php:7.4.16-apache-buster

WORKDIR /var/www/cmd
RUN curl -L https://install.meilisearch.com | sh
RUN mv /var/www/cmd/meilisearch /usr/local/bin/meilisearch
#RUN wget https://get.symfony.com/cli/installer -O - | bash
#RUN curl -L https://get.symfony.com/cli/installer | sh
#RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

WORKDIR /var/www/app

#COPY ./ ./

#установить yarn ?  composer?
# скачать гитом проект?!
#CMD ["meilisearch"]
#CMD ["./symfony"]
#CMD sleep 300
CMD yes | php bin/console app:daemon:start
