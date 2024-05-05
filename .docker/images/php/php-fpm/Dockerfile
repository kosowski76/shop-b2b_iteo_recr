ARG HOST_IMAGE
FROM ${HOST_IMAGE} as base

RUN echo ${TARGET_PHP_VERSION}
#RUN apk add --no-cache --update \
#        php-fpm~=$TARGET_PHP_VERSION
# make "php-fpm" as command available by creating a symlink
RUN ln -s /usr/sbin/php-fpm82 /usr/bin/php-fpm
    
COPY ./images/php/php-fpm/php-fpm.d/ /etc/php82/php-fpm.d/
COPY ./images/php/php-fpm/conf.d/${ENV}/* /etc/php82/conf.d/
COPY ./images/php/php-fpm/conf.d/zz-app-fpm.ini /etc/php82/conf.d/

RUN sed -i "s/__APP_USER_NAME/$APP_USER_NAME/" /etc/php82/php-fpm.d/* \
    && sed -i "s/__APP_GROUP_NAME/$APP_GROUP_NAME/" /etc/php82/php-fpm.d/*

USER $APP_USER_NAME

EXPOSE 9000 9003

CMD ["php-fpm", "-F"]

FROM base as local