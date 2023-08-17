#!/bin/bash

set -ex

git clone https://github.com/guptarajesh/CodeIgnitor-3.2-Login-Register-Dashboard-CRUD-Operations
mv CodeIgnitor-3.2-Login-Register-Dashboard-CRUD-Operations ${DOCROOT}
sudo chown -R ${sshUsername}: ${DOCROOT}

## 1. 建立 database from *.sql
DBNAME='ci30cb_wt_final_login'
mysql -uroot -pjack5899 -e "Create Database IF NOT EXISTS ${DBNAME} CHARACTER SET utf8mb4 Collate utf8mb4_unicode_ci;"

# e10adc3949ba59abbe56e057f20f883e
# 現在 123456, 要改成
# "\$2y\$10\$FPA1J0EFv71fJ1EJi.8uBOLvjCtZ.0yMmwyRdF88vpx803irClaxu"
#mysql -uroot -pjack5899 ${DBNAME} < ${DOCROOT}/DB/ci30cb_wt_final_login.sql
# 改正 password 的錯誤
mysql -uroot -pjack5899 ${DBNAME} < /vagrant/Thirdparty/CRUD/ci30cb_wt_final_login-clickap.sql

## 2. 準備 database
cd ${DOCROOT}/application/config
sed -i "s/\(.*'username'[ ]\).*/\1=> 'root',/g" database.php
sed -i "s/\(.*'password'[ ]\).*/\1=> 'jack5899',/g" database.php
sed -i "s/\(.*'database'[ ]\).*/\1=> '${DBNAME}',/g" database.php

## 3. base_url 要改
    # 先把 base_url 清空
sed -i "s/\(.*config\['base_url'\][ ]\).*/\1= '';/g" ${DOCROOT}/application/config/config.php
cd ${DOCROOT}/application/config
sed -i "/.*config\['base_url'\] = '';/a\$config['base_url'] = \$base;" config.php
sed -i "/.*config\['base_url'\] = '';/a\$base .= str_replace(basename(\$_SERVER['SCRIPT_NAME']),\"\",\$_SERVER['SCRIPT_NAME']);" config.php
sed -i "/.*config\['base_url'\] = '';/a\$base  = \"http://\".\$_SERVER['HTTP_HOST'];" config.php

#echo "*** You can login via: admin@example.com / 123456"
echo "*** You can login via: admin@example.com / jack5899"

echo "*** done."