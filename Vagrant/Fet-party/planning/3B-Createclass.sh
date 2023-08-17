#!/bin/bash

set -ex

# planning/createclass
#Usage: FetDcsdPhy=/var/www/html/phy/base/admin DOCROOT=/var/www/html/ci3rjsphy bash 3B-Createclass.sh
#export FetDcsdPhy="/home/${sshUsername}/FetDcsdPhy/base/admin"

# /home/vagrant/FetDcsdPhy/base/admin

## 3B
mkdir -p ${DOCROOT}/application/controllers/planning/
cd ${FetDcsdPhy}/application
cp controllers/planning/Createclass.php ${DOCROOT}/application/controllers/planning/

mkdir -p ${DOCROOT}/application/models/{planning,data}
cp models/planning/Createclass_model.php ${DOCROOT}/application/models/planning/
cp models/planning/Setclass_model.php ${DOCROOT}/application/models/planning/
cp models/planning/Booking_place_model.php ${DOCROOT}/application/models/planning/
mkdir -p ${DOCROOT}/application/views/planning/createclass
cp views/planning/createclass/list.php ${DOCROOT}/application/views/planning/createclass/
cp views/planning/createclass/edit.php ${DOCROOT}/application/views/planning/createclass/
cp views/planning/createclass/form.php ${DOCROOT}/application/views/planning/createclass/

# cp models/data/Second_category_model.php ${DOCROOT}/application/models/data/ # 因為 HMVC, 所以改到 modules/data/models/
mkdir -p ${DOCROOT}/application/modules/data/models
cp models/data/Second_category_model.php ${DOCROOT}/application/modules/data/models/

## 3A
cp controllers/planning/Setclass.php ${DOCROOT}/application/controllers/planning/
cp models/planning/Set_startdate_model.php ${DOCROOT}/application/models/planning/
mkdir -p ${DOCROOT}/application/views/planning/setclass
cp views/planning/setclass/list.php ${DOCROOT}/application/views/planning/setclass/
cp views/planning/createclass/add.php ${DOCROOT}/application/views/planning/createclass/

## Ajax (planning/set_startdate/getSecondCategory)
mkdir -p ${DOCROOT}/application/controllers/planning
cp controllers/planning/Set_startdate.php ${DOCROOT}/application/controllers/planning/

## 3C
cp controllers/planning/Annualplan_select.php ${DOCROOT}/application/controllers/planning/
cp models/planning/Annualplan_model.php ${DOCROOT}/application/models/planning/
mkdir -p ${DOCROOT}/application/views/planning/annualplan
cp views/planning/annualplan/list.php ${DOCROOT}/application/views/planning/annualplan/


## autoload
echo "array_push(\$autoload['libraries'], 'pagination');" | tee -a ${DOCROOT}/application/config/autoload.php
## Add helper('form') (CodeIgniter Form Helpers) 加 form_helper 到 autoload.php 
echo "array_push(\$autoload['helper'], 'form');" | tee -a ${DOCROOT}/application/config/autoload.php

echo "*** 3B-Createclass transfer is done."
