#!/bin/bash

set -ex
#PROJECTID=mdl29
#DOCROOT="/var/www/html/mdl29"
MODVER=29
MODPORT=80
ADMMAIL=martin@click-ap.com
DBNAME="moodle29"
DATAROOT='/var/www/moodle29data'

# download moodle from web
sudo rm -rf moodle ${DOCROOT} 
if [ ! -f "moodle-latest-$MODVER.tgz" ]; then
    wget -q https://download.moodle.org/download.php/direct/stable$MODVER/moodle-latest-$MODVER.tgz
fi
tar zxf moodle-latest-$MODVER.tgz # remark v: verbose
sudo mv moodle ${DOCROOT}
sudo mkdir -p ${DATAROOT}
sudo chown vagrant:www-data -R ${DOCROOT} ${DATAROOT}
if [ ! -f "${DOCROOT}/config.php" ]; then
    mysql -uroot -pjack5899 -hlocalhost mysql -e "Drop Database IF EXISTS ${DBNAME}; FLUSH PRIVILEGES;"
    cd ${DOCROOT}
    php admin/cli/install.php --agree-license --non-interactive --lang=en --wwwroot=http://localhost:$MODPORT/$PROJECTID --dataroot=${DATAROOT} --dbtype=mariadb --dbhost=localhost --dbname=$DBNAME --dbuser=root --dbpass=jack5899 --fullname=$PROJECTID --shortname=$PROJECTID --adminpass=Jack5899! --adminemail=$ADMMAIL
    #php admin/cli/install.php --agree-license --non-interactive --lang=en --wwwroot=http://localhost/moodle --dataroot=/var/www/moodledata --dbtype=mariadb --dbhost=localhost --dbname=moodle --dbuser=root --dbpass=jack5899 --fullname=Moodle29 --shortname=mod29 --adminpass=Jack5899! --adminemail=martin@click-ap.com
fi
# add $CFG->cachejs, Prevent JS caching
sudo sed -i '/$CFG->admin/a $CFG->debugdisplay = 1;' config.php
sudo sed -i '/$CFG->admin/a $CFG->debug = (E_ALL | E_STRICT);' config.php
sudo sed -i '/$CFG->admin/a $CFG->cachejs   = false;' config.php
sudo sed -i '/$CFG->admin/a $CFG->yuicomboloading = false;' config.php

sudo chown www-data ${DOCROOT}/config.php
#sudo chmod 440 ${DOCROOT}/config.php

echo "*** done."