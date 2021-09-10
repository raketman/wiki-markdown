FROM centos:8

RUN yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm
RUN yum -y install https://rpms.remirepo.net/enterprise/remi-release-8.rpm
RUN yum -y install yum-utils

RUN dnf -y module enable php:remi-7.4
RUN dnf -y install php php-cli php-common php-xml

WORKDIR /var/www/app
RUN yum -y install git
RUN git clone https://github.com/raketman/wiki-markdown.git .

WORKDIR /var/www/download
RUN curl -L https://install.meilisearch.com > /var/www/download/meilisearch

WORKDIR /var/www/app
RUN yum -y install php-pecl-json
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN php composer.phar install -o  --verbose --ignore-platform-reqs --no-dev --optimize-autoloader --prefer-dist -n

EXPOSE 8000

RUN rm -rf /var/www/app/run/pid/*

RUN sh /var/www/download/meilisearch

CMD yes | rm -rf /var/www/app/run/* | php bin/console app:daemon:start
