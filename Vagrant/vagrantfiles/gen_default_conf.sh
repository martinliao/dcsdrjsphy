#!/bin/bash

#set -ex
# Set up Nginx Server Blocks(Virtual Hosts)

echo "Current user: [`whoami`], Current dir: [`pwd`]..."
echo "NGINXPORT=${NGINXPORT}"
echo "DOCROOT=${DOCROOT}"
echo "SERVERNAME=${SERVERNAME}"
echo "FILESIZE=${FILESIZE}"

export PHPVERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")

cat <<EOF | sudo tee /etc/nginx/sites-available/${SERVERNAME}
server {
    listen ${NGINXPORT};
    listen [::]:${NGINXPORT};

    server_name ${SERVERNAME};
    server_tokens off;

    root ${DOCROOT};
    index index.php index.html index.htm;

    client_max_body_size ${FILESIZE};
    autoindex off;
    location / {
        try_files \$uri \$uri/ =404;
    }

    location ~ [^/].php(/|$) {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php${PHPVERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    include /etc/nginx/location.d/*.conf;
}
EOF

echo "Nginx virtual host setup is done."