#!/bin/bash

# Vagrant Usage:
    #debian.vm.provision :shell, inline: """
    #  cp -r /CI3/CI3Adminlte ~/Theme
    #""", privileged: false
    #debian.vm.provision :shell, path: "vagrantfiles/ci3-theme.sh", privileged: false, env: {"DOCROOT" => "/var/www/html/#{PROJECTID}", "sshUsername" => "vagrant" }
# 但因為 有 SimpleLayout, 所以這個 item放棄了.(11Apr2023)

# Theme 使用 ci3-adminlte(github.com/i4mnoon3/ci3-adminlte.git)
# ci3-adminlte, 需要把 database.php 設定好, base_url 要修, 最後 gen_virtualhost 設定好 index.php, done. 8Apr2023

if [ ! -d "ci3-adminlte" ]; then
    git clone https://github.com/i4mnoon3/ci3-adminlte
fi

cd ci3-adminlte
#cp application/controllers/User.php ${DOCROOT}/application/controllers/
# User.php 需要修改 MY_Controller
cp ~/Theme/application/controllers/User.php ${DOCROOT}/application/controllers/

cp application/libraries/Layout.php ${DOCROOT}/application/libraries/
cp -r application/views/AdminLTE ${DOCROOT}/application/views/
cp -r public ${DOCROOT}/

# 去掉 demo.js 內的 ga.
cd ${DOCROOT}/public/themes/AdminLTE/dist/js
sed -i "/^ga[(].*/d" demo.js

# 修改 autoload.php 在 libraries 內加入 layout.
cd ${DOCROOT}/application/config
sed -i "s/\(.*autoload\['libraries'\][ ]\).*/\1= array\('database','session','layout'\);/g" autoload.php

cd /var/www/html

sudo chown -R ${sshUsername}:www-data ${DOCROOT}

cat << EOF
*** 開啟 user/dashboard or user/dashboard2 檢視 Theme(ci3-adminlte) 結果:
        http://localhost/ci3/user/dashboard
        http://localhost/ci3/user/dashboard2
EOF

# 但因為 有 SimpleLayout, 所以這個 item放棄了.(11Apr2023)
