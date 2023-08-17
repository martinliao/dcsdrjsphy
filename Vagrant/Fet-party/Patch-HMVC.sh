#!/bin/bash

# 因為要整合 Fet版的 MY_Controller, 所以 HMVC 要改
# Usage: DOCROOT=/var/www/html/ci3rjsphy FetDcsdPhy=/var/www/html/phy/base/admin bash Patch-HMVC.sh
#   export FetDcsdPhy="/home/${sshUsername}/FetDcsdPhy/base/admin/" (/home/vagrant/FetDcsdPhy/base/admin)

## 1. 改HMVC的core, 從 MY => MI.
cd ${DOCROOT}/application/core
mv MY_Controller.php MI_Controller.php
mv MY_Loader.php MI_Loader.php
mv MY_Router.php MI_Router.php
sed -i 's/class[ ]MY_Controller/class MI_Controller/g' MI_Controller.php
sed -i 's/class[ ]MY_Loader/class MI_Loader/g' MI_Loader.php
sed -i 's/class[ ]MY_Router/class MI_Router/g' MI_Router.php

sed -i 's/class[ ]MY_Router/class MI_Router/g' MI_Router.php
#class MY_Controller extends CI_Controller
#class MY_Controller extends MX_Controller

## 2. 改 Fet/controller/core
cd ${FetDcsdPhy}/application/core
cp MY_Controller.php ${DOCROOT}/application/core/
cp MY_Model.php ${DOCROOT}/application/core/
sed -i 's/extends[ ]CI_Controller/extends MX_Controller/g' ${DOCROOT}/application/core/MI_Controller.php
##    也要改 MY_Controller.php, 改繼承自 MI_Controller.
sed -i 's/extends[ ]CI_Controller/extends MI_Controller/g' ${DOCROOT}/application/core/MY_Controller.php

#ToDo:
#class BackendController extends MI_Controller
#class BackendController extends MI_Controller
#class JavascriptController extends MI_Controller
sed -i 's/extends[ ]MY_Controller/extends MI_Controller/g' ${DOCROOT}/application/core/Backend_Controller.php
sed -i 's/extends[ ]MY_Controller/extends MI_Controller/g' ${DOCROOT}/application/core/Frontend_Controller.php
sed -i 's/extends[ ]MY_Controller/extends MI_Controller/g' ${DOCROOT}/application/core/Javascript_Controller.php

## 3. 改 config subclass_prefix, MY_ -> MI_
# \$config['subclass_prefix'] = 'MI_'; // 'MY_';
sed -i "s/\(.*config\['subclass_prefix'\][ ]\).*/\1= 'MI_';/g" ${DOCROOT}/application/config/config.php

#echo "array_push(\$autoload['libraries'], 'session');" | tee -a ${DOCROOT}/application/config/autoload.php

echo "*** Patch HMVC is done"