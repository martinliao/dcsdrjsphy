#!/bin/bash

# Usage: DOCROOT=/var/www/html/ci3Phy23B FetDcsdPhy=/var/www/html/phy/base/admin bash ~/Patch-Layout.sh
#   export FetDcsdPhy="/home/${sshUsername}/FetDcsdPhy/base/admin/" (/home/vagrant/FetDcsdPhy/base/admin)

###     1. 修 sidebar 問題(因HMVC,所以要改 flags)
sed -i "s/\(\$this->flags\)->\([a-z]*\)/\$flags['\2']/g" ${DOCROOT}/application/views/common/sidebar.php
###     sidebar 可以 sed 改, 但 navbar.php 請手動改!!

###     2. 改 navbar.php
<<comment
eg.
    \$this->flags->user['co_usrnick'] 改成 \$flags['user']['co_usrnick']
eg.
    <?=!empty($this->flags->user['co_usrnick'])?$this->flags->user['co_usrnick']:$this->flags->user['name'];?>
要改成 <?=empty($flags['user']['name']) ? $flags['user']['co_usrnick'] : $flags['user']['name']; ?>
, 所以以下:
sed -i "s/\([$].*flags\)->\([a-z]*\)/\$flags['\2']/g" ${DOCROOT}/application/views/common/navbar.php
不能用...
comment
echo "      請注意, navbar.php 要手動改."

###     3. Copy Layout(libraries)
#cp ${FetDcsdPhy}/application/libraries/Layout.php ${DOCROOT}/application/libraries/ # 不能用, 要改
cp /FetOldParty/Libraries/Layout.php ${DOCROOT}/application/libraries/

echo "      請注意, navbar.php 要手動改."
echo "*** Layout patch is done"