#!/bin/bash

#sed -i "s/\(.*config\['csrf_protection'\][ ]\).*/\1= FALSE;/g" $DOCROOT/application/config/config.php

## Fix Welcome.php, 略過 ReCaptcha 的檢查.
<<comment
# /var/www/html/phy/base/admin/application/controllers
在 Welcome.php, line 69. 把:
if(empty($post['g-recaptcha-response'])){
改成
if ( (strcmp(ENVIRONMENT, 'production') == 0) && empty($post['g-recaptcha-response'])){
comment

cd ${DOCROOT}/application/controllers
sed -i "s/\(.*if[ ]*[(][ ]*\)\(empty(.*post\['g-recaptcha-response'\])\).*/\1 (strcmp(ENVIRONMENT, 'production') == 0) \&\& \2){ /g" Welcome.php

echo "*** done."