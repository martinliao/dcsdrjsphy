#!/bin/bash

# 這是單純測專案:
# export PROJECTID=ci3adminlte
# export DOCROOT=/var/www/html/ci3adminlte
# ci3-adminlte, 不需要把 database.php 設定好, base_url 要修, 最後 gen_virtualhost 設定好 index.php, done. 8Apr2023

cd /var/www/html
if [ ! -d "${DOCROOT}" ]; then
    git clone https://github.com/i4mnoon3/ci3-adminlte
    sudo mv ci3-adminlte ${PROJECTID}
fi
sudo chown -R ${sshUsername}:www-data ${PROJECTID}


cd ${DOCROOT}/application/config
sed -i "s/\(.*'username'[ ]\).*/\1=> 'root',/g" database.php
sed -i "s/\(.*'password'[ ]\).*/\1=> 'jack5899',/g" database.php
sed -i "s/\(.*'database'[ ]\).*/\1=> '${PROJECTID}',/g" database.php

# 去掉 demo.js 內的 ga
cd ${DOCROOT}/public/themes/AdminLTE/dist/js
sed -i "/^ga[(].*/d" demo.js

mysql -uroot -pjack5899 -e "Create Database IF NOT EXISTS ${PROJECTID} CHARACTER SET utf8mb4 Collate utf8mb4_unicode_ci;"

echo "*** done."

cat << EOF
*** 請記得檢查/修改 config.php 內的 base_url
    \$base  = "http://".\$_SERVER['HTTP_HOST'];
    \$base .= str_replace(basename(\$_SERVER['SCRIPT_NAME']),"",\$_SERVER['SCRIPT_NAME']);
    \$config['base_url'] = \$base;
EOF

# 先把 base_url 清空
sed -i "s/\(.*config\['base_url'\][ ]\).*/\1= '';/g" ${DOCROOT}/application/config/config.php
# sed -i "s/\(.*config\['base_url'\][ ]\).*/\1= '';/g" application/config/config.php
cd ${DOCROOT}/application/config
sed -i "/.*config\['base_url'\] = '';/a\$config['base_url'] = \$base;" config.php
sed -i "/.*config\['base_url'\] = '';/a\$base .= str_replace(basename(\$_SERVER['SCRIPT_NAME']),\"\",\$_SERVER['SCRIPT_NAME']);" config.php
sed -i "/.*config\['base_url'\] = '';/a\$base  = \"http://\".\$_SERVER['HTTP_HOST'];" config.php

echo "*** done."