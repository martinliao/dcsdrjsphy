#!/bin/bash

set -ex

git clone https://github.com/oxygenfox/reactadmin.git
mv reactadmin ${DOCROOT}
sudo chown -R vagrant: ${DOCROOT}

mysql -uroot -pjack5899 -e "Create Database IF NOT EXISTS ${PROJECTID} CHARACTER SET utf8mb4 Collate utf8mb4_unicode_ci;"
mysql -uroot -pjack5899 ${PROJECTID} < /vagrant/Thirdparty/reactadmin/database.sql

cp /vagrant/Thirdparty/reactadmin/database.php ${DOCROOT}/application/config/
cp /vagrant/Thirdparty/reactadmin/database.php /var/www/html/reactadmin/application/config/

## Fix Bug
mv ${DOCROOT}/application/modules/home/views/Index.php ${DOCROOT}/application/modules/home/views/index.php
mv ${DOCROOT}/application/views/_layout/Auth ${DOCROOT}/application/views/_layout/auth
mv ${DOCROOT}/application/views/_layout/Admin ${DOCROOT}/application/views/_layout/admin

#password#$2y$10$1LfJVrr1ItplFlCGiVWpgepuhLDmvkhWBrl7PzTiEDWdpmnQzN5Wy
#123456#$2y$10$4IPLOB4CrQkgNOhDGDeIc.yUYLpnCypmegplvsQKa.RoMgRQhVD9e

sudo chown -R vagrant: /var/www/html/reactadmin

echo "*** reactadmin is done."