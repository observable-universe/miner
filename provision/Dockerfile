FROM php:7.1-apache

MAINTAINER Observable Universe <we-need-an-email@sap.com>

# Update and source packages
RUN apt-get update -y \
  && apt-get install -y \
  zlib1g-dev \
  libjpeg-dev \
  libpng-dev \
  libfreetype6-dev \
  libapache2-mod-fcgid \
  libcurl4-gnutls-dev \
  libxml2-dev \
  supervisor \
  libmcrypt-dev \
  libjpeg62-turbo-dev \
  libvpx-dev \
  vim \
  awscli \
  && rm -rf /var/lib/apt/lists/*

# Phpize extensions
RUN docker-php-ext-install -j$(nproc) \
 pdo_mysql \
 gd \
 mysqli

# Get xdebug from pecl
RUN pecl install \
  xdebug \
  && docker-php-ext-enable \
  xdebug

# Enable apache mods
RUN a2enmod \
  headers \
  proxy \
  proxy_http \
  proxy_fcgi \
  rewrite \
  ssl \
  fcgid \
  include

# Prep directories
RUN mkdir /srv/logs
RUN chown www-data:www-data /srv -R
COPY . /opt/provision

RUN mkdir -p /srv/logs
RUN chown www-data:www-data /srv -R

# Move config files into place
RUN cp /opt/provision/apache/apache2.conf /etc/apache2/apache2.conf
RUN cp /opt/provision/apache/sites-enabled/miner.conf /etc/apache2/sites-enabled/miner.conf
RUN cp /opt/provision/apache/mods-available/fcgid.conf /etc/apache2/mods-available/fcgid.conf
# Move ssl cert chain
RUN mkdir -p /etc/pki/local
RUN cp /opt/provision/apache/ca.* /etc/pki/local/
# Move fpm configs
RUN cp /opt/provision/fpm/pool.d/miner.conf /etc/php7/fpm/pool.d/miner.conf
RUN cp /opt/provision/fpm/php.ini /etc/php7/fpm/php.ini
RUN cat /opt/provision/fpm/conf.d/30-xdebug.ini | sed "s|_extensionFilePath|$(find /usr/local/lib/php/extensions/ -name 'xdebug.so')|g" > /etc/php7/fpm/conf.d/30-xdebug.ini

CMD service php7-fpm start && service apache2 start && tail -f /var/log/apache2/error.log
