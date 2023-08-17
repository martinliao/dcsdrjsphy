# CI_LTE
CodeIgniter 3 + AdminLTE 3+ Bootsrap 4 + Jquery 3
## Installation
1. Unzip the package.
2. Upload the folders and files to your server.
3. Open the `application/config/common/dp_config.php` file with a text editor and set options.
4. Create a table named `ci_adminlte` and inject the data from the `ci_adminlte.sql` file.
5. Change if necessary the connection information to your database in the `application/config/database.php` file.

git clone https://github.com/hayalolsam/CI_LTE/
(看起來是從domProjects改來的: https://github.com/domProjects/CI-AdminLTE )
domProjects還有 https://github.com/martinliao/CI-Breadcrumb 及 https://github.com/martinliao/AdminTPL 兩個可以參考.


### Features特徵

* 它用的 Auth 是 ion_auth
* 分為 Admin 及 Public, 像是前後台概念.
  * 有2個 Controller(Admin,/Public) 繼承自 MY_Controller
* 有 Template(JK:不是很優雅, 我改變想法Apr2023), templates在 views/admin內
  * 但感覺 Template 還比 ion_auth 還有價值.
  * template->admin_render 及 auth_render ,取代 load->view

* 另外有 dp_config.php, 主導了UI路徑; 但為什麼不直接在 config.php 內就好?
* 雖然用 AdminLTE 但是 Bootstrat 好像沒有設定/計的很好?(Active Menu 沒有作用), UI 還是 c3lrdco 比較好!!
* Ajax/Chat 只有在 admin/dashboard 時才會正常.

* 比較特別的是有 Mobile_detect 在 libraries/common 內.
  * 特別介紹: Mobile_detect(https://github.com/serbanghita/Mobile-Detect), demo: https://demo.mobiledetect.net/
* 有 breadcrumbs (class from: https://github.com/nobuti/Codeigniter-breadcrumbs)

# CI_LTE

![](public/ci.png)
![](public/adminlte.png)

It's a simple themed CodeIgniter 3 with AdminLTE.

它用的 Auth 是 ion_auth

## Feature
* 使用 template 
* 有 {home,auth,admin,public} 佈局(但不是 layout)
    template->admin_render 及 auth_render ,取代 load->view
* 有多國語言
* 有 breadcrumbs (class from: https://github.com/nobuti/Codeigniter-breadcrumbs)

### 版本

相對於 domProject 的版本, AdminLTE, Bootstrap, jQuery 都比較新

AdminLTE	3.0.0 alpha 2
Bootstrap	4.3.1
jQuery	3.1.4

## 準備

### Create DB

```
mysql -uroot -pjack5899 -e 'Create Database IF NOT EXISTS `ci_lte` CHARACTER SET utf8mb4 Collate utf8mb4_unicode_ci;'
```

### dp_config

有個比較特別的 - 在 autoload.php 內:

```
$autoload['packages'] = array(APPPATH.'third_party/ion_auth');
$autoload['libraries'] = array('form_validation', 'ion_auth', 'template', 'common/mobile_detect');
$autoload['helper'] = array('array', 'language', 'url');
$autoload['config'] = array('common/dp_config', 'common/dp_language');
$autoload['language'] = array();
$autoload['model'] = array('common/prefs_model');
```

## Installation
1. Unzip the package.
2. Upload the folders and files to your server.
3. Open the `application/config/common/dp_config.php` file with a text editor and set options.
4. Create a table named `ci_adminlte` and inject the data from the `ci_adminlte.sql` file.
5. Change if necessary the connection information to your database in the `application/config/database.php` file.
### Login
 * Email : `admin@admin.com` / Password : `password`
