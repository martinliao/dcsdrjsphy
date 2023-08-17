#!/bin/bash

# 因為要整合 Fet版的 認證, 所以要改 Auth(從 Dcsd的Welcome來的)

# Usage: DOCROOT=/var/www/html/ci3Phy23B FetDcsdPhy=/var/www/html/phy/base/admin bash ~/Patch-Auth.sh
#   export FetDcsdPhy="/home/${sshUsername}/FetDcsdPhy/base/admin/" (/home/vagrant/FetDcsdPhy/base/admin)

#  1. 不繼承 MY_Controller, 因為太多處理及資訊不是 Login 需要的(增加負擔).
#  2. 改用 SmartACL, 使用專業的ACL, 不自己去處理 membership, lock/unlock, 帳號啟用, attempt 次數等, 資安條件另外管理.
#  3. view 的部份收歛成1個, 且不使用 Layout(library, 增加Login負擔)

#   1. 己經整合舊版(2022)Welcome的 Login, 使用SmartACL作為 login 的判斷.
cp /FetOldParty/Auth/controllers/Auth.php ${DOCROOT}/application/modules/Auth/controllers/Auth.php
# 舊版(2022)的登錄view
cp /FetOldParty/Auth/views/old-login.php ${DOCROOT}/application/modules/Auth/views/

#   2. copy New core/MY_Controller.php
cp /FetOldParty/core/MY_Controller.php ${DOCROOT}/application/core/

echo "*** Auth patch is done"