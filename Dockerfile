#FROM php:7.4-cli-alpine3.13
FROM php:7.4-alpine

RUN mkdir -p /var/lib/meilisearch
RUN curl -L -o /var/lib/meilisearch/meilisearch  https://install.meilisearch.com | sh

RUN mkdir -p /var/lib/symfony
RUN curl -L -o /var/lib/symfony https://get.symfony.com/cli/installer
#RUN  wget -c https://get.symfony.com/cli/installer  -O - | sh
#RUN curl -sS https://get.symfony.com/cli/installer | sh
#RUN curl -L -o /var/lib/symfony https://get.symfony.com/cli/installer
#RUN cat /var/lib/symfony
#RUN sh /var/lib/symfony

WORKDIR /var/www/app

#RUN curl -L  -o /var/lib/symfony https://get.symfony.com/cli/installer

COPY ./ ./

#установить yarn ?  composer?
# скачать гитом проект?!

CMD sleep 300
#CMD ["/usr/bin/php /var/www/app/bin/console app:daemon:start"]
