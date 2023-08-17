#!/bin/bash

# 目前沒用到, 合併到 gen_fpm_pool_conf.sh 內. Apr2023.
export PHPVERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")

sudo sed -i 's|pm.max_children[ ]*=.*|pm.max_children = 1000|' /etc/php/${PHPVERSION}/fpm/pool.d/www.conf
sudo sed -i 's|pm.start_servers[ ]*=.*|pm.start_servers = 200|' /etc/php/${PHPVERSION}/fpm/pool.d/www.conf
sudo sed -i 's|pm.min_spare_servers[ ]*=.*|pm.min_spare_servers = 200|' /etc/php/${PHPVERSION}/fpm/pool.d/www.conf
sudo sed -i 's|pm.max_spare_servers[ ]*=.*|pm.max_spare_servers = 1000|' /etc/php/${PHPVERSION}/fpm/pool.d/www.conf
sudo sed -i 's|.*pm.max_requests[ ]*=.*|pm.max_requests = 2000|' /etc/php/${PHPVERSION}/fpm/pool.d/www.conf
sudo systemctl enable php${PHPVERSION}-fpm

echo "*** php${PHPVERSION}-fpm configuration setting is done."
