#FROM php:7.4-cli-alpine3.13
#FROM alpine:latest
#FROM centos:8
FROM centos:7
#FROM php:7.4.16-cli-buster
#FROM php:7.4.16-apache-buster

WORKDIR /var/www/cmd
RUN curl -L https://install.meilisearch.com | sh
RUN mv /var/www/cmd/meilisearch /usr/local/bin/meilisearch
#RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN curl -L https://get.symfony.com/cli/installer | sh
RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

RUN yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm
RUN yum -y install https://rpms.remirepo.net/enterprise/remi-release-7.rpm
RUN yum -y install yum-utils
RUN yum-config-manager --enable remi-php74
RUN yum -y update
#RUN yum -y update --allowerasing --skip-broken --nobest
RUN yum -y install php php-cli
RUN yum -y install php php-xml
RUN php -v


WORKDIR /var/www/app

COPY ./ ./

#установить yarn ?  composer?
# скачать гитом проект?!
#CMD ["meilisearch"]
#CMD ["symfony serve"]
CMD sleep 3000
#CMD yes | php bin/console app:daemon:start
