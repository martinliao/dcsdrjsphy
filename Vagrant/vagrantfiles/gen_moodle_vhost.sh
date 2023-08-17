#!/bin/bash

# ref: https://gist.github.com/srmklive/67d550cfac8bab530c69ef95a8b28d09

cat <<EOF | sudo tee /etc/nginx/virtualhost.d/${PROJECTID}.conf;
     location /moodle {
        #alias /var/www/html/moodle;
        try_files $uri $uri/ =404;

        location ~ \.php$ {
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $request_filename;
            fastcgi_pass unix:/run/php/php7.3-fpm.sock;
        }
    }
EOF

# 在 default.conf 必須要有 include /etc/nginx/virtualhost.d/*.conf;

{ sudo nginx -t; } && { sudo systemctl restart nginx; sudo systemctl restart php7.3-fpm; }