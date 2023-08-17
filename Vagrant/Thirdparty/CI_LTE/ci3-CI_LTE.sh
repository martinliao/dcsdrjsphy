#!/bin/bash

set -ex

git clone https://github.com/hayalolsam/CI_LTE
mv CI_LTE ${DOCROOT}

## 1. 準備 database
cd ${DOCROOT}/application/config
sed -i "s/\(.*'username'[ ]\).*/\1=> 'root',/g" database.php
sed -i "s/\(.*'password'[ ]\).*/\1=> 'jack5899',/g" database.php
sed -i "s/\(.*'database'[ ]\).*/\1=> '${PROJECTID}',/g" database.php

## 2. 建立 database from *.sql
mysql -uroot -pjack5899 -e "Create Database IF NOT EXISTS ${PROJECTID} CHARACTER SET utf8mb4 Collate utf8mb4_unicode_ci;"
mysql -uroot -pjack5899 ${PROJECTID} < ${DOCROOT}/ci_adminlte.sql

## 3. base_url 要改
    # 先把 base_url 清空
sed -i "s/\(.*config\['base_url'\][ ]\).*/\1= '';/g" ${DOCROOT}/application/config/common/dp_config.php
cd ${DOCROOT}/application/config/common
sed -i "/.*config\['base_url'\] = '';/a\$config['base_url'] = \$base;" dp_config.php
sed -i "/.*config\['base_url'\] = '';/a\$base .= str_replace(basename(\$_SERVER['SCRIPT_NAME']),\"\",\$_SERVER['SCRIPT_NAME']);" dp_config.php
sed -i "/.*config\['base_url'\] = '';/a\$base  = \"http://\".\$_SERVER['HTTP_HOST'];" dp_config.php

## 4. Session save_path 有問題, 需要改
sed -i "s/\(.*config\['sess_save_path'\][ ]\).*/\1= NULL;/g" ${DOCROOT}/application/config/config.php

echo "*** You can login via: admin@admin.com / password"
echo "*** done."