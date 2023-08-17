#!/bin/bash

MOODLEDIR='d:/php/Moodle29/'
#echo $PROJECTID
#echo $DOCROOT

cp ${MOODLEDIR}libs/weblib_29_helper.php ${DOCROOT}/application/helpers/weblib_helper.php
# Remove redirect() and is_https 重覆定義. 
# 或是
# 加上 if (!function_exists('xxx')) 檢查?
cd ${DOCROOT}/application/config
#sed -i "s/\(.*autoload\['helpers'\][ ]\).*/\1= array\('weblib_helper''\);/g" autoload.php
echo "array_push(\$autoload['helper'], 'weblib_helper');" >> autoload.php

echo "*** Moodle 擴充完成"