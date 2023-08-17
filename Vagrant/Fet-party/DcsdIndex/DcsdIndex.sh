 #!/bin/bash

set -ex

#Usage: FetDcsdPhy='/var/www/html/phy/base/admin/' DOCROOT='/var/www/html/ci3rjsphy' sshUsername='vagrant' bash DcsdIndex.sh
## 定 Fet版本的根目錄
#export FetDcsdPhy="/home/${sshUsername}/FetDcsdPhy/base/admin/"
# /home/vagrant/FetDcsdPhy/base/admin

cd ${FetDcsdPhy}
cp application/controllers/Dcsdindex.php ${DOCROOT}/application/controllers/
cp application/models/Dcsdindex_model.php ${DOCROOT}/application/models/
mkdir -p ${DOCROOT}/application/models/create_class
cp application/models/create_class/Course_sch_model.php ${DOCROOT}/application/models/create_class
cp -r application/views/common ${DOCROOT}/application/views/
cp application/views/dcsdindex.php ${DOCROOT}/application/views/

## system
cd ${FetDcsdPhy}
cp -r application/models/system ${DOCROOT}/application/models/


## Constants(常數)
cat <<EOF | tee -a ${DOCROOT}/application/config/constants.php
// DCSD, BF 2022
\$scheme = \$_SERVER['REQUEST_SCHEME'] . '://';
\$dirx= explode("/", \$_SERVER['PHP_SELF']);
\$httpRoot= \$scheme . \$_SERVER['HTTP_HOST'] . '/' . \$dirx[1] . '/';
defined('HTTP_ROOT')           OR define('HTTP_ROOT', \$httpRoot);
defined('HTTP_ROOT')           OR define('HTTP_ROOT', base_url('/'));
defined('HTTP_STATIC')         OR define('HTTP_STATIC', HTTP_ROOT . 'static/');
defined('HTTP_CSS')            OR define('HTTP_CSS', HTTP_STATIC . 'css/');
defined('HTTP_JS')             OR define('HTTP_JS', HTTP_STATIC . 'js/');
defined('HTTP_IMG')            OR define('HTTP_IMG', HTTP_STATIC . 'img/');
defined('HTTP_PLUGIN')         OR define('HTTP_PLUGIN', HTTP_STATIC . 'plugin/');
defined('HTTP_MEDIA')          OR define('HTTP_MEDIA', HTTP_ROOT . "media/");
EOF

## Static(js,css,img,plugin)
mkdir -p ${DOCROOT}/static/{css,img,js,plugin}
cd ${FetDcsdPhy}
cp -r static/plugin/font-awesome ${DOCROOT}/static/plugin/
cp -r static/plugin/animate ${DOCROOT}/static/plugin/
cp -r static/plugin/bootstrap ${DOCROOT}/static/plugin/
cp -r static/plugin/datepicker ${DOCROOT}/static/plugin/
cp -r static/plugin/metisMenu ${DOCROOT}/static/plugin/
cp -r static/plugin/jStarbox ${DOCROOT}/static/plugin/
cp -r static/plugin/select2 ${DOCROOT}/static/plugin/
cp static/plugin/jquery-1.12.4.min.js ${DOCROOT}/static/plugin/
cp static/plugin/jquery.mousewheel-3.0.6.pack.js ${DOCROOT}/static/plugin/
cp -r static/plugin/noty ${DOCROOT}/static/plugin/
cp -r static/plugin/fancybox ${DOCROOT}/static/plugin/
cp static/plugin/jquery.highlight-3.js ${DOCROOT}/static/plugin/
cp static/plugin/moment-with-locales.js ${DOCROOT}/static/plugin/
cp -r static/plugin/jquery.blockUI-2.7.0 ${DOCROOT}/static/plugin/

cp static/js/my.js ${DOCROOT}/static/js/
cp static/js/common.js ${DOCROOT}/static/js/

### CSS
cp static/css/sb-admin-2.css ${DOCROOT}/static/css/
cp static/css/style.css ${DOCROOT}/static/css/
cp static/css/calendar.css ${DOCROOT}/static/css/
cp static/css/sidebar_anime.css ${DOCROOT}/static/css/
cp static/css/drag_and_drop.css ${DOCROOT}/static/css/

sudo chown -R ${sshUsername}:www-data ${DOCROOT}/static


##   因為Fet使用 $this->flags(object) 在 views({navbar,sidebar}.php) 內; 所以要改 array, 而且在view 要改成 $flags['user'] 這種格式.
##      在 MY_Controller.php, index 內 , 加入 $this->data['flags'] = (array)$this->flags;
cat <<EOF
    MY_Controller: 
        在 \$this->initFlags(); 後加上: 
        \$this->data['flags'] = \$this->flags;
    views:
        \$this->flags 換成 \$flags
EOF

## Copy helper/common
cd ${FetDcsdPhy}/application
cp helpers/common_helper.php ${DOCROOT}/application/helpers/

## Copy database.php
cd ${FetDcsdPhy}/application/config
cp -r development ${DOCROOT}/application/config/

##
## Fix CSRF, Phy 的 CSRF 還搞不清楚...先改成 FALSE
sed -i "s/\(.*config\['csrf_protection'\][ ]\).*/\1= FALSE;/g" $DOCROOT/application/config/config.php

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

## 修改 Dcsdindex.php 符合 SmartACL
cp /FetOldParty/DcsdIndex/Dcsdindex.php ${DOCROOT}/application/controllers/


echo "*** Fet Dcsd old transfer is done."