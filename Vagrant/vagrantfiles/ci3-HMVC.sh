#!/bin/bash

#set -ex
# 有找到比較接近原始(Wiredesignz)的版本: https://github.com/martinliao/codeigniter-modular-extensions-hmvc
#    Wiredesignz did an awesome job with the HMVC extension, 但是它沒有取 module 為路由, 相反的, 要去設定routes.php 才能用.
#    而且它說改善了效能(cache), 也改善了路由.
# 但還沒有試用.

wget -qO HMVC.zip https://github.com/martinliao/CodeIgniter-HMVC/archive/refs/tags/0.3.zip
unzip HMVC.zip > /dev/null 2>&1
cd CodeIgniter-HMVC-0.3
## 1. 取 core 擴充
cp application/core/{MY_Router.php,MY_Loader.php,MY_Controller.php,Frontend_Controller.php,Backend_Controller.php} ${DOCROOT}/application/core/
## 2. 取 modules 範例(Backend,Frontend,welcome)
mkdir -p ${DOCROOT}/application/modules
cp -r application/modules/{welcome,Frontend,Backend} ${DOCROOT}/application/modules/
## 3. 取 MX 擴充(third_party)
cp -r application/third_party/MX ${DOCROOT}/application/third_party/
sudo chown -R ${sshUsername}:www-data ${DOCROOT}
# remove old Welcome?
#rm -f ${DOCROOT}/application/controllers/Welcome.php
#rm -f ${DOCROOT}/application/views/welcome_message.php
## 4. 在 config.php 加入 modules 設定.
cat <<EOF | tee -a ${DOCROOT}/application/config/config.php

/*
|--------------------------------------------------------------------------
| Set HMVC Modules Location
|--------------------------------------------------------------------------
|
*/
\$config['modules_locations'] = array(
    APPPATH.'modules/' => '../modules/',
);
EOF

## 5. 加 url_helper 到 autoload.php 
sed -i "s/\(.*autoload\['helper'\][ ]\).*/\1= array\('url'\);/g" ${DOCROOT}/application/config/autoload.php

## 5. 修改 composer.json 加入 filp/whoops^2.5
cat ${DOCROOT}/composer.json | jq '.require += { "filp/whoops": "^2.5" }' | tee -i ${DOCROOT}/composer.json
# Composer autoload

## 6. 在 index.php development 加入 whoops(協助錯誤訊息UI, 在開發環境)
cd ${DOCROOT}
sed -i "/[<?]php/a require_once __DIR__.'\/vendor\/autoload.php';" index.php
sed -i '/.*display_errors.*[ ]1);/a\\t\t$whoops->register();' index.php
sed -i '/.*display_errors.*[ ]1);/a\\t\t$whoops->pushHandler(new Whoops\\Handler\\PrettyPageHandler());' index.php
sed -i '/.*display_errors.*[ ]1);/a\\t\t$whoops = new \\Whoops\\Run;' index.php
sed -i '/.*display_errors.*[ ]1);/a\\t\terror_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);' index.php

### 7. composer install
cd ${DOCROOT}
rm -f composer.lock
composer install

echo "*** done."
# https://github.com/N3Cr0N/HMVC 這個 HMVC 是從 wiredesignz 來的(Bucket站打不開), 
#   N3Cr0N/HMVC 只有到 0.1.zip (5Feb2019)
#   N3Cr0N/CodeIgniter-HMVC 有到 0.3.zip (24Jun2019), 所以這個脚本用的.