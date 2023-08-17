#!/bin/bash

#echo $PROJECTID
#echo $DOCROOT

rm -rf $DOCROOT
if [ ! -d 'codeignitor-requirejs-backbone' ]; then
    git clone https://github.com/martinliao/codeignitor-requirejs-backbone.git
fi
sudo mv codeignitor-requirejs-backbone /var/www/html/

sudo chown -R ${sshUsername}:www-data ${DOCROOT}

## 0. 先把 base_url 清空.
sed -i "s/\(.*config\['base_url'\][ ]\).*/\1= '';/g" ${DOCROOT}/application/config/config.php
## 0. 改預設 base_url.
cd ${DOCROOT}/application/config
sed -i "/.*config\['base_url'\] = '';/a\$config['base_url'] = rtrim(\$base, '/');" config.php
sed -i "/.*config\['base_url'\] = '';/a\$base .= str_replace(basename(\$_SERVER['SCRIPT_NAME']),\"\",\$_SERVER['SCRIPT_NAME']);" config.php
sed -i "/.*config\['base_url'\] = '';/a\$base  = \"http://\".\$_SERVER['HTTP_HOST'];" config.php

## 1. 修 bug
sed -i 's|/js/main|js/main|g' ${DOCROOT}/application/views/templates/header.php

echo "*** codeignitor-requirejs-backbone is done."