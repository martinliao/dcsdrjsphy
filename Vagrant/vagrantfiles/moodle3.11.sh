#!/bin/bash

set -ex
<<comment
    # 在 Vagrantfile 的使用方法:
    debian.vm.provision :shell, path: "vagrantfiles/moodle3.11.sh", privileged: false, env: {"DOCROOT" => "/var/www/html/mdl311", "PROJECTID" => "mdl311" }
    # DOCROOT='/var/www/html/moodle' PROJECTID="mod311" bash ~/mdl311.sh
    # 加 moodle(virtualhost)
    debian.vm.provision :shell, path: "vagrantfiles/gen_moodle_vhost.sh", privileged: false, env: {"DOCROOT" => "/var/www/html/mdl311", "PROJECTID" => "mdl311", "sshUsername" => "vagrant" }
    # DOCROOT='/var/www/html/mdl311' PROJECTID='mdl311' sshUsername='vagrant' bash gen_moodle_vhost.sh
comment


#PROJECTID=moodle
#DOCROOT="/var/www/html/moodle"
MODVER=311
MODPORT=80
ADMMAIL=martin@click-ap.com
#DBGSO = dbg-php-7.3.so
CUSTDBNAME="moodle311"

# download moodle from web
sudo rm -rf moodle ${DOCROOT}
#wget --no-verbose https://download.moodle.org/download.php/direct/stable$MODVER/moodle-latest-$MODVER.tgz
wget --no-verbose https://download.moodle.org/download.php/direct/stable311/moodle-3.11.13.tgz -O moodle-latest-$MODVER.tgz
tar zxf moodle-latest-$MODVER.tgz
sudo mv moodle ${DOCROOT}
sudo mkdir -p /var/www/moodledata
# tool_brcli
#wget --no-verbose https://moodle.org/plugins/download.php/19178/tool_brcli_moodle38_2019031500.zip -O tool_brcli.zip
#unzip tool_brcli.zip -d /var/www/html/$PROJECTID/admin/tool/

sudo chown -R vagrant:www-data ${DOCROOT} /var/www/moodledata

cd ${DOCROOT}
export DBNAMESTRING=$CUSTDBNAME
php admin/cli/install.php --agree-license --non-interactive --lang=en --wwwroot=http://localhost:$MODPORT/$PROJECTID --dataroot=/var/www/moodledata --dbtype=mariadb --dbhost=localhost --dbname=${CUSTDBNAME} --dbuser=root --dbpass=jack5899 --fullname=$PROJECTID --shortname=$PROJECTID --adminpass=Jack5899! --adminemail=$ADMMAIL

# add $CFG->cachejs, Prevent JS caching
sudo sed -i '/$CFG->admin/a $CFG->debugdisplay = 1;' config.php
sudo sed -i '/$CFG->admin/a $CFG->debug = (E_ALL | E_STRICT);' config.php
sudo sed -i '/$CFG->admin/a $CFG->cachejs   = false;' config.php
sudo sed -i '/$CFG->admin/a $CFG->yuicomboloading = false;' config.php

#sudo chown apache -R ${DOCROOT} /var/www/moodledata
sudo chown www-data ${DOCROOT}/config.php
#sudo chmod 440 ${DOCROOT}/config.php

<<comment
# Ensure folder permission
sudo mkdir -p /var/www/html/$WEBDIR/node_modules
sudo chown vagrant -R /var/www/html/$WEBDIR/node_modules
cd /var/www/html/$WEBDIR
npm install
npm i grunt --save-dev
comment

sudo chown -R vagrant:www-data ${DOCROOT}

cd ${DOCROOT}
wget -qO- '--header=PRIVATE-TOKEN: ogR8q_kz2zzHoR_LVKKB' 'https://gitlab.com/api/v4/projects/25075312/repository/files/langimport.php/raw?ref=master' | php
# course_path.csv
wget '--header=PRIVATE-TOKEN: ogR8q_kz2zzHoR_LVKKB' 'https://gitlab.com/api/v4/projects/25075312/repository/files/course_path.csv/raw?ref=master' -qO course_path.csv
#wget -qO- '--header=PRIVATE-TOKEN: ogR8q_kz2zzHoR_LVKKB' 'https://gitlab.com/api/v4/projects/25075312/repository/files/uploadcourse.php/raw?ref=master' | MODE=createorupdate php
wget -qO- '--header=PRIVATE-TOKEN: ogR8q_kz2zzHoR_LVKKB' 'https://gitlab.com/api/v4/projects/25075312/repository/files/importuser36.php/raw?ref=master' | MODE=createorupdate php
# user_enrol.csv
wget '--header=PRIVATE-TOKEN: ogR8q_kz2zzHoR_LVKKB' 'https://gitlab.com/api/v4/projects/25075312/repository/files/user_enrol.csv/raw?ref=master' -qO user1.csv
wget --no-cache -qO - '--header=PRIVATE-TOKEN: ogR8q_kz2zzHoR_LVKKB' 'https://gitlab.com/api/v4/projects/25075312/repository/files/clickap_uuser.php/raw?ref=master' | php -- -m=createnew -f=./user1.csv '-p=Jack5899!'

echo "Moodle courses & users created, done."
