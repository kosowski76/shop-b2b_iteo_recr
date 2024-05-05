ARG ALPINE_VERSION
ARG COMPOSER_VERSION
FROM composer:${COMPOSER_VERSION} as composer
FROM alpine:${ALPINE_VERSION} as base

# make build args available as ENV variables to downstream images
# so that we don't have to pass the same build args again
ARG ALPINE_VERSION
ARG APP_CODE_PATH
ARG APP_USER_ID
ARG APP_USER_NAME
ARG APP_GROUP_ID
ARG APP_GROUP_NAME
ARG ENV
ARG TARGET_PHP_VERSION
ENV ALPINE_VERSION=${ALPINE_VERSION}
ENV APP_USER_ID=${APP_USER_ID}
ENV APP_USER_NAME=${APP_USER_NAME}
ENV APP_GROUP_ID=${APP_GROUP_ID}
ENV APP_GROUP_NAME=${APP_GROUP_NAME}
ENV APP_CODE_PATH=${APP_CODE_PATH}
ENV ENV=${ENV}
ENV TARGET_PHP_VERSION=${TARGET_PHP_VERSION}

COPY ./.shared/scripts/ /tmp/scripts/
RUN chmod +x -R /tmp/scripts/

# set timezone
#ARG TZ=UTC
#RUN /tmp/scripts/timezone.sh ${TZ}

# add users

RUN addgroup -g $APP_GROUP_ID $APP_GROUP_NAME && \
    adduser -D -u $APP_USER_ID -s /bin/bash $APP_USER_NAME -G $APP_GROUP_NAME && \
    mkdir -p $APP_CODE_PATH && \
    chown $APP_USER_NAME: $APP_CODE_PATH
    
# RUN sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd

RUN apk add --update --no-cache \
        bash \
        curl-dev \
  #      make \
        oniguruma-dev \
        openssh \
        openssl-dev \
        strace \
        sudo \
        tar \
        vim \
        unzip \
        xz
RUN apk add --update --no-cache libxml2-dev libcurl

RUN apk --update-cache add ca-certificates && \
    apk add --no-cache gcc g++ make cmake pkgconf sqlite-dev

# install PHP
RUN apk add --update --no-cache php82 \
    php82-cli \
    php82-curl \
    # php-dom~=${TARGET_PHP_VERSION} \
    php82-fpm \
    # php-mbstring~=${TARGET_PHP_VERSION} \
    # php-pdo_mysql~=${TARGET_PHP_VERSION} \
    # php-pdo~=${TARGET_PHP_VERSION} \
    # php-phar~=${TARGET_PHP_VERSION} \
    # php-redis~=${TARGET_PHP_VERSION} \
    # php-xml~=${TARGET_PHP_VERSION} \
    php82-xdebug
    #php8~=${TARGET_PHP_VERSION} \
# make "php" as command available by creating a symlink
# https://github.com/codecasts/php-alpine/issues/20#issuecomment-350477886    
#RUN ln -s /usr/bin/php82 /usr/bin/php

# make bash default shell
RUN sed -e 's;/bin/ash$;/bin/bash;g' -i /etc/passwd
# RUN sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd

COPY ./images/php/php-host/conf.d/zz-app.ini /etc/php82/conf.d/
COPY ./images/php/php-host/conf.d/zz-app-${ENV}.ini /etc/php82/conf.d/

COPY ./images/php/php-host/.bashrc /home/${APP_USER_NAME}/.bashrc
COPY ./images/php/php-host/.bashrc /root/.bashrc

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

WORKDIR $APP_CODE_PATH

FROM base as local

# add app user to sudoers
# see https://ostechnix.com/add-delete-and-grant-sudo-privileges-to-users-in-alpine-linux/ for adding sudo
# see https://askubuntu.com/a/340669 for not requiring a sudo pw
RUN echo "root ALL=(ALL) NOPASSWD: ALL " | tee -a "/etc/sudoers.d/users" && \
    echo "${APP_USER_NAME} ALL=(ALL) NOPASSWD: ALL " | tee -a "/etc/sudoers.d/users"

RUN apk add --no-cache --update \
        mysql-client \
        redis \
    #    php82-xdebug~=${TARGET_PHP_VERSION} \
    # ensure that xdebug is not enabled by default
    && rm -f /etc/php82/conf.d/00_xdebug.ini