#!/bin/bash
set -e

PROJECT="$1"

export DEBIAN_FRONTEND=noninteractive
apt-get -y update
apt-get -y install language-pack-en-base dos2unix
export LC_ALL=en_US.UTF-8
export LC_CTYPE=en_US.UTF-8

if file -b /vagrant_data/provision/files/canary.txt | grep -q "with CRLF line terminators"; then
    echo "Your git checkout may have the wrong line terminators"
    echo "Please set core.autocrlf to 'input' and reset your working tree"
    echo "Aborting..."
    exit 255
fi

cp /vagrant_data/provision/files/apt.backports /etc/apt/preferences.d/01-automatic-backports
apt-add-repository -y ppa:ondrej/php
locale-gen
debconf-set-selections /vagrant_data/provision/files/apt.preseed
apt-get -y update
apt-get -y autoremove
apt-get -y install vim git unzip \
    apache2 libapache2-mod-fastcgi \
    mysql-server mysql-client \
    php5.6-fpm php5.6-cli php5.6-dev php5.6-gd php5.6-memcached php5.6-mysql php5.6-pdo-mysql php5.6-gd \
    php5.6-intl php5.6-json php5.6-xdebug php5.6-mbstring php5.6-curl php5.6-zip php5.6-xml \
    memcached imagemagick

phpdismod -v 5.6 -s cli xdebug
a2enmod proxy_fcgi rewrite headers
a2enconf php5.6-fpm
cp /vagrant_data/provision/files/default-site.conf /etc/apache2/sites-available/000-default.conf
cat /vagrant_data/provision/files/site.conf | sed -E 's/\{\{\s?PROJECT\s?\}\}/'"${PROJECT}"'/g' > /etc/apache2/sites-available/001-${PROJECT}.dev.local.conf
a2ensite 001-${PROJECT}.dev.local.conf
service apache2 restart
# TODO configure xdebug
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
composer config -g github-oauth.github.com 42ef1f55f3d4b34ed280261b7f0fce35e4beee5d
# composer global require twig/twig

rsync -rt /vagrant_data/provision/files/dot/ /home/vagrant/ && chown -R vagrant:vagrant /home/vagrant/

mysql -uroot -pvagrant -e 'CREATE DATABASE IF NOT EXISTS drupal8 DEFAULT CHARSET utf8'
mysql -uroot -pvagrant -e 'GRANT ALL ON drupal8.* TO drupal8@localhost IDENTIFIED BY "123"'

# using "vagrant ssh", you'll need to setup your local env using these command (if you haven't already done so)
# cd /vagrant_data
# sudo -u vagrant composer install
# sudo -u phing make

