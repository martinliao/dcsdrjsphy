#!/bin/bash

#set -ex

#rm -rf codeigniter-smarty-acl-* smarty-acl 
wget -O smarty-acl.zip https://github.com/martinliao/codeigniter-smarty-acl/archive/refs/tags/V1.2.zip
unzip smarty-acl.zip > /dev/null 2>&1
mv -f codeigniter-smarty-acl-* smarty-acl

### 0. Prepare準備作業, migrations
cp -r smarty-acl/SmartyAcl ${DOCROOT}/application/third_party/
mkdir -p ${DOCROOT}/application/migrations
cp smarty-acl/001_Create_Smarty_Acl.php ${DOCROOT}/application/migrations

mysql -uroot -pjack5899 -e "Create Database IF NOT EXISTS ${PROJECTID} CHARACTER SET utf8mb4 Collate utf8mb4_unicode_ci;"
## 設定 database
cd ${DOCROOT}/application/config
sed -i "s/\(.*'username'[ ]\).*/\1=> 'root',/g" database.php
sed -i "s/\(.*'password'[ ]\).*/\1=> 'jack5899',/g" database.php
sed -i "s/\(.*'database'[ ]\).*/\1=> '${PROJECTID}',/g" database.php
## 設定 autoload
cd ${DOCROOT}/application/config
sed -i "s/\(.*autoload\['libraries'\][ ]\).*/\1= array\('database','session'\);/g" autoload.php
sed -i "s/\(.*autoload\['packages'\][ ]\).*/\1= array\(APPPATH.'third_party\/DevelBar',APPPATH.'third_party\/SmartyAcl'\);/g" autoload.php
echo "*** SmartyACL 下載、設定及DB準備完成."

### 1. 準備 Migration(升級, importdatabase)
cd ${DOCROOT}/application/config
sed -i "s/\(.*config\['migration_enabled'\][ ]\).*/\1= TRUE;/g" migration.php
#    change timestamp to sequential
sed -i "s/\(.*config\['migration_type'\][ ]\).*/\1= 'sequential';/g" migration.php

#     1-b. 加路由(DB升級: importdatabase)
echo "// SmartyaACL route" | tee -a ${DOCROOT}/application/config/routes.php
echo "\$route['importdatabase'] = 'welcome/importdatabase';" | tee -a ${DOCROOT}/application/config/routes.php
cp -r ~/SmartyACL/modules/welcome ${DOCROOT}/application/modules/
cat <<EOF
*** 準備 Migration(升級, importdatabase) done.
    請用 http://localhost/${PROJECTID}/importdatabase 進行資料庫升級
EOF

### 2. 加 Admin,Login,Logout(而已,測試用)
cp -r ~/SmartyACL/modules/Admin ${DOCROOT}/application/modules/
cp -r ~/SmartyACL/modules/AuthAdmin ${DOCROOT}/application/modules/
cp -r ~/SmartyACL/views/* ${DOCROOT}/application/views/
#      2-b. 加路由; 最少 UI, 只有 admin, login, logout
cat <<EOF | tee -a ${DOCROOT}/application/config/routes.php
\$route['admin'] = 'Admin/index';
\$route['admin/login'] = 'AuthAdmin/index';
\$route['admin/logout'] = 'AuthAdmin/logout';
EOF

cd ${DOCROOT}/application/config
sed -i "/.*config\['base_url'\] = '';/a\$config['base_url'] = rtrim(\$base, '/');" config.php
sed -i "/.*config\['base_url'\] = '';/a\$base .= str_replace(basename(\$_SERVER['SCRIPT_NAME']),\"\",\$_SERVER['SCRIPT_NAME']);" config.php
sed -i "/.*config\['base_url'\] = '';/a\$base  = \"http://\".\$_SERVER['HTTP_HOST'];" config.php
echo "*** Login/Logout, done"

### 3. 加 Auth
cp -r ~/SmartyACL/modules/Auth ${DOCROOT}/application/modules/
# 改 welcome
cp -r ~/SmartyACL/modules/welcome ${DOCROOT}/application/modules/

#     3-b. 加路由; Auth UI(使用者UI)
cat <<EOF | tee -a ${DOCROOT}/application/config/routes.php
\$route['login']    = 'Auth/login';
\$route['logout']   = 'Auth/logout';
\$route['register'] = 'Auth/register';
\$route['account']  = 'welcome/account';
EOF

cp -r ~/SmartyACL/views/account.php ${DOCROOT}/application/views/
echo "*** Auth模組, done"

### 4. Modules模組
cp -r ~/SmartyACL/modules/Admin/views/modules.php ${DOCROOT}/application/modules/Admin/views/
cp -r ~/SmartyACL/modules/Admin/views/modules_form.php ${DOCROOT}/application/modules/Admin/views/
#     4-b. 加路由; Modules
cat <<EOF | tee -a ${DOCROOT}/application/config/routes.php
//Modules
\$route['admin/modules'] = 'Admin/modules';
\$route['admin/modules/create'] = 'Admin/module_create';
\$route['admin/modules/edit/(:num)'] = 'Admin/module_edit/\$1';
\$route['admin/modules/delete/(:num)'] = 'Admin/module_delete/\$1';
EOF
echo "*** Modules模組, done"

### 5. Roles角色
cp -r ~/SmartyACL/modules/Admin/views/roles.php ${DOCROOT}/application/modules/Admin/views/
cp -r ~/SmartyACL/modules/Admin/views/roles_form.php ${DOCROOT}/application/modules/Admin/views/
#     5-b. 加路由; Roles
cat <<EOF | tee -a ${DOCROOT}/application/config/routes.php
//Roles
\$route['admin/roles'] = 'Admin/roles';
\$route['admin/roles/create'] = 'Admin/role_create';
\$route['admin/roles/edit/(:num)'] = 'Admin/role_edit/\$1';
\$route['admin/roles/delete/(:num)'] = 'Admin/role_delete/\$1';
EOF
echo "*** Roles角色, done"

### 6. Admins
cp -r ~/SmartyACL/modules/Admin/views/admins.php ${DOCROOT}/application/modules/Admin/views/
cp -r ~/SmartyACL/modules/Admin/views/admins_form.php ${DOCROOT}/application/modules/Admin/views/
#     6-b. 加路由; Admins
cat <<EOF | tee -a ${DOCROOT}/application/config/routes.php
//Admins
\$route['admin/admins'] = 'Admin/admins';
\$route['admin/admins/create'] = 'Admin/admin_create';
\$route['admin/admins/edit/(:num)'] = 'Admin/admin_edit/\$1';
\$route['admin/admins/delete/(:num)'] = 'Admin/admin_delete/\$1';
EOF
echo "*** Admins管理者, done"

### 7. Users一般使用者
cp -r ~/SmartyACL/modules/Admin/views/users.php ${DOCROOT}/application/modules/Admin/views/
cp -r ~/SmartyACL/modules/Admin/views/users_form.php ${DOCROOT}/application/modules/Admin/views/
#     7-b. 加路由; Users
cat <<EOF | tee -a ${DOCROOT}/application/config/routes.php
//Users
\$route['admin/users'] = 'Admin/users';
\$route['admin/users/create'] = 'Admin/user_create';
\$route['admin/users/edit/(:num)'] = 'Admin/user_edit/\$1';
\$route['admin/users/delete/(:num)'] = 'Admin/user_delete/\$1';
EOF
echo "*** Users一般使用者, done"

sudo chown -R ${sshUsername}:www-data ${DOCROOT}

###
cat << EOF
*** 請記得檢查/修改 config.php 內的 base_url
    \$base  = "http://".\$_SERVER['HTTP_HOST'];
    \$base .= str_replace(basename(\$_SERVER['SCRIPT_NAME']),"",\$_SERVER['SCRIPT_NAME']);
    \$config['base_url'] = rtrim(\$base, '/');
*** done.
EOF