 #!/bin/bash

set -ex

#Usage: FetDcsdPhy='/var/www/html/phy/base/admin/' DOCROOT='/var/www/html/ci3rjsphy' sshUsername='vagrant' bash DcsdIndex.sh
## 定 Fet版本的根目錄
#export FetDcsdPhy=/var/www/html/phy/base/admin or  "/home/${sshUsername}/FetDcsdPhy/base/admin/"
#   data 是小寫

# 1. Controller
cd ${FetDcsdPhy}/application/controllers/
mkdir -p ${DOCROOT}/application/modules/data/controllers
cp data/Teacher_manger*.php ${DOCROOT}/application/modules/data/controllers/
cp data/Student_manger.php ${DOCROOT}/application/modules/data/controllers
cp data/Human_authority.php ${DOCROOT}/application/modules/data/controllers
cp data/Human_personnel.php ${DOCROOT}/application/modules/data/controllers

# 2. Model
cp ${FetDcsdPhy}/application/models/Teacher_log_model.php ${DOCROOT}/application/models/
cd ${FetDcsdPhy}/application/models/
mkdir -p ${DOCROOT}/application/modules/data/models
cp data/Teacher_model*.php ${DOCROOT}/application/modules/data/models/
cp data/Student_model.php ${DOCROOT}/application/modules/data/models/
cp data/Course_code_model.php ${DOCROOT}/application/modules/data/models/
cp data/Hire_category_model.php ${DOCROOT}/application/modules/data/models/
cp data/Bank_code_model.php ${DOCROOT}/application/modules/data/models/
cp data/Job_title_model.php ${DOCROOT}/application/modules/data/models/
cp data/Out_gov_model.php ${DOCROOT}/application/modules/data/models/
# Second_category_model.php 在 3B 已經 copy
cp data/Second_category_model.php ${DOCROOT}/application/modules/data/models/
cp data/Human_authority_model.php ${DOCROOT}/application/modules/data/models/

# 3. View
mkdir -p ${DOCROOT}/application/modules/data/views/{teacher_manger,student_manger,human_authority,human_personnel,teacher_manger_2}
cd ${FetDcsdPhy}/application/views
cp data/teacher_manger/list.php ${DOCROOT}/application/modules/data/views/teacher_manger/
cp data/student_manger/list.php ${DOCROOT}/application/modules/data/views/student_manger/
cp data/human_authority/list.php ${DOCROOT}/application/modules/data/views/human_authority/
cp data/human_personnel/{edit,list}.php ${DOCROOT}/application/modules/data/views/human_personnel/
cp data/teacher_manger_2/list_2.php ${DOCROOT}/application/modules/data/views/teacher_manger_2/

# 4. 在 common_helper.php 加 function getQueryString().
cat /FetOldParty/data/getQueryString.php | tee -a ${DOCROOT}/application/helpers/common_helper.php

# 5. 修 Teacher_manger.php 
#sed -i "s/\$this->getQueryString(/getQueryString(\$this->data['filter'], /g" ${DOCROOT}/application/modules/data/controllers/Teacher_manger.php
# 因為 Teacher_manger.php 改回 MY_Controller, 所以不用修 getQueryString.

sudo chown -R ${sshUsername}:www-data ${DOCROOT}/static


<<comment #不用加路由! 23Apr2023.
# 6. 加路由; updated 經實驗: 改成 HMVC
cat <<EOF | tee -a ${DOCROOT}/application/config/routes.php

// DCSD - Data
\$route['data/teacher_manger']   = 'data/teacher_manger/index';
\$route['data/student_manger']   = 'data/student_manger/index';
\$route['data/human_authority']  = 'data/human_authority/index';
EOF
comment

echo "*** Fet 選單2, (2A~2G) old transfer is done."