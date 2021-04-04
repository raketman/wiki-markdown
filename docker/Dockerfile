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
RUN yum -y install git
RUN git clone https://github.com/raketman/wiki-markdown.git .

WORKDIR /var/www/app/app
RUN yum -y install epel-release
RUN curl -sL https://rpm.nodesource.com/setup_10.x | bash
RUN yum -y install nodejs

RUN node --version
RUN npm --save  install

RUN npm run build
RUN npm run generate


WORKDIR /var/www/app
RUN rm -rf /var/www/app/app

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
#RUN php composer.phar install -o --no-dev
#RUN php composer.phar global require hirak/prestissimo
RUN php composer.phar install -o  --verbose --ignore-platform-reqs --no-dev --optimize-autoloader --prefer-dist -n


WORKDIR /var/www/app/public

#COPY ./ ./

#установить yarn ?  composer?
# скачать гитом проект?!
#CMD ["meilisearch"]
#CMD ["symfony serve"]
#CMD ["php -S localhost:8000 router.php"]
#CMD sleep 3000
#CMD yes | php bin/console app:daemon:start
