FROM wolnosciowiec/docker-php-app:arm7hf

ENV APP_ENV="prod" \
    DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db" \
    SITE_TITLE="Anarchist banner rotator" \
    SITE_DESCRIPTION="Shows always up-to-date list of anarchist pages on every of them. http://iwa-ait.org, http://zsp.net.pl" \
    HEADER_ALLOW_ORIGIN="" \
    HEADER_ALLOW_CREDENTIALS=true \
    HEADER_MAX_AGE=3600 \
    HEADER_ALLOW_METHODS="" \
    HEADER_ALLOW_HEADERS=""

ADD . /var/www/html
RUN [ "cross-build-start" ]

RUN cd /var/www/html \
    && chown www-data:www-data ./ -R \
    && su www-data -s /bin/bash -c "make deploy"

RUN [ "cross-build-end" ]
