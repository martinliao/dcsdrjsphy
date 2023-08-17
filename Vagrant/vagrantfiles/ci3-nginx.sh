#!/bin/bash

# Codeigniter 3 及 Nginx site(virtualhost)的設定.
#set -ex

CIVERSION='3.1.13'
rm -f ${CIVERSION}.zip
rm -rf ${DOCROOT}
wget -q https://github.com/bcit-ci/CodeIgniter/archive/refs/tags/3.1.13.zip
unzip ${CIVERSION}.zip > /dev/null 2>&1
sudo mv CodeIgniter-${CIVERSION} ${DOCROOT}

<<comment
cat <<EOF | sudo tee ${DOCROOT}/.htaccess
DirectoryIndex index.php
RewriteEngine on
RewriteCond \$1 !^(index\.php|images|css|js|robots\.txt|favicon\.ico)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)\$ ./index.php/\$1 [L,QSA]
EOF
comment

sudo chown -R ${sshUsername}:www-data ${DOCROOT}

## 1. 加入 vendor-autoload
sed -i "s/\(.*config\['composer_autoload'\][ ]\).*/\1= APPPATH . '..\/vendor\/autoload.php';/g" ${DOCROOT}/application/config/config.php

# Add virtualhost.d for virtual-host
sudo rm -rf /etc/nginx/virtualhost.d/* && sudo mkdir -p /etc/nginx/virtualhost.d/
# Add location.d for location
sudo rm -rf /etc/nginx/location.d/* && sudo mkdir -p /etc/nginx/location.d/

#cat <<EOF | sudo tee /etc/nginx/location.d/${PROJECTID}.conf
#    location /dataroot/ {
#        internal;
#        alias ${DATAROOT}/;
#    }
#EOF

echo "*** done."