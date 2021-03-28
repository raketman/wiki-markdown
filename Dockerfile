FROM php:7.4-cli-alpine3.13

RUN mkdir -p /var/lib/meilisearch
RUN curl -L -o /var/lib/meilisearch  https://install.meilisearch.com | sh
#RUN  wget https://get.symfony.com/cli/installer -O - | sh

WORKDIR /var/www/app

RUN curl -L  -o /var/lib/symfony https://get.symfony.com/cli/installer

COPY ./ ./

#установить yarn ?  composer?
# скачать гитом проект?!

CMD sleep 300
#CMD ["/usr/bin/php /var/www/app/bin/console app:daemon:start"]
