#!/usr/bin/env bash

sudo apt-get update

# Install MySQL


# Setting Locales
echo "###########################"
echo "##### Setting Locales #####"
echo "###########################"


export LANGUAGE=en_US.UTF-8
export LANG=en_US.UTF-8
export LC_ALL=en_US.UTF-8
locale-gen en_US.UTF-8
dpkg-reconfigure locales

sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password password secret'
sudo debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password secret'
sudo apt-get install -y mysql-server-5.6 mysql-client-5.6

#
# Configure MySQL Remote Access
#
MYSQLAUTH="--user=root --password=secret"
mysql $MYSQLAUTH -e "GRANT ALL ON *.* TO root@'localhost' IDENTIFIED BY 'secret' WITH GRANT OPTION;"
mysql $MYSQLAUTH -e "CREATE USER 'magento'@'localhost' IDENTIFIED BY 'secret';"
mysql $MYSQLAUTH -e "GRANT ALL ON *.* TO 'magento'@'localhost' IDENTIFIED BY 'secret' WITH GRANT OPTION;"
mysql $MYSQLAUTH -e "GRANT ALL ON *.* TO 'magento'@'%' IDENTIFIED BY 'secret' WITH GRANT OPTION;"
mysql $MYSQLAUTH -e "FLUSH PRIVILEGES;"
mysql $MYSQLAUTH -e "CREATE DATABASE magento;"

# Install PHP
sudo add-apt-repository -y ppa:ondrej/php
sudo apt-get update
sudo apt-get install -y php7.0-fpm php7.0-mysql php7.0-cli php7.0-mcrypt php7.0-curl php7.0-gd php7.0-intl php7.0-xsl php7.0-zip php7.0-mbstring curl git


sudo add-apt-repository -y ppa:nginx/stable

sudo apt-get update

sudo apt-get install -y nginx
# Install Composer.
cd /tmp
curl -sS https://getcomposer.org/installer | php
sudo cp composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer



sudo mkdir /etc/nginx/ssl
sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/nginx/ssl/magento-self.key -out /etc/nginx/ssl/magento-self.crt

# append AllowOverride to nginx Config File
echo "#######################################"
echo "##### CREATING NGINX CONFIG FILE #####"
echo "#######################################"
echo "
upstream fastcgi_backend {  
    server unix:/var/run/php/php7.0-fpm.sock;
}

server {  
    listen 443 ssl;
    server_name YOUR-IP-OR-URL;
    set $MAGE_ROOT /var/www/html;
    set $MAGE_MODE developer;
    ssl_certificate /etc/nginx/ssl/magento-self.crt;
    ssl_certificate_key /etc/nginx/ssl/magento-self.key;
    include /var/www/html/nginx.conf.sample;
}

server {  
    listen 80;
    server_name intelliamor.dev;
    set $MAGE_ROOT /var/www/html;
    set $MAGE_MODE default;
    include /var/www/html/nginx.conf.sample;
}
" > /etc/nginx/sites-available/default

# sudo crontab -u vagrant -e

# */1 * * * * /usr/bin/php -c /etc/php/7.0/cli/php.ini /var/www/magento/bin/magento cron:run > /var/www/magento/var/log/magento.cron.log&
# */1 * * * * /usr/bin/php -c /etc/php/7.0/cli/php.ini /var/www/magento/update/cron.php > /var/www/magento/var/log/update.cron.log&
# */1 * * * * /usr/bin/php -c /etc/php/7.0/cli/php.ini /var/www/magento/bin/magento setup:cron:run > /var/www/magento/var/log/setup.cron.log&


