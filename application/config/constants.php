<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code


/*
|--------------------------------------------------------------------------
| HTTP_VARS from DCSD(FET version), BF 2022
|--------------------------------------------------------------------------
| 舊版佈景大量使用了 常數 (Constant, https://www.php.net/manual/en/language.constants.php)
| 一段較少看到把常數用在路徑, 而且是URL路徑. 因為在 Cli 的情況無法取得 REQUEST_SCHEME, HTTP_HOST 等參數, 所以就無法完成路徑自動化.(必須寫死)
| 且 PHPUnit 無法測...
| 未來建議改用 config 達成(eg. $assets_dir, $plugins_dir, $framework_dir, $avatar_dir...)同樣的效果. 
| 
| 但是為了相容性, 所以目前還是保留, Updated, Apr2023, martin.
*/ 
$scheme = $_SERVER['REQUEST_SCHEME'] . '://';
$dirx= explode("/", $_SERVER['PHP_SELF']);
$httpRoot= $scheme . $_SERVER['HTTP_HOST'] . '/' . $dirx[1] . '/';

defined('HTTP_ROOT')           OR define('HTTP_ROOT', $httpRoot);
defined('HTTP_STATIC')         OR define('HTTP_STATIC', HTTP_ROOT . 'static/');
defined('HTTP_CSS')            OR define('HTTP_CSS', HTTP_STATIC . 'css/');
defined('HTTP_JS')             OR define('HTTP_JS', HTTP_STATIC . 'js/');
defined('HTTP_IMG')            OR define('HTTP_IMG', HTTP_STATIC . 'img/');
defined('HTTP_PLUGIN')         OR define('HTTP_PLUGIN', HTTP_STATIC . 'plugin/');

/*
|--------------------------------------------------------------------------
| USER DEFINED CONSTANTS
|--------------------------------------------------------------------------
*/
define('ROLE_ADMIN',                            '1');
define('ROLE_MANAGER',                         	'2');
define('ROLE_EMPLOYEE',                         '3');

//9B 課程講座時間建檔
if (!defined('DIR_ROOT')) define('DIR_ROOT', dirname(dirname(__FILE__)) . '/');
if (!defined('DIR_UPLOAD_COURSE_SCHEDULE')) define('DIR_UPLOAD_COURSE_SCHEDULE', DIR_ROOT . 'files/upload_course_schedule/');
//13A 講義上傳 files/media/upload
if (!defined('DIR_MEDIA')) define('DIR_MEDIA', FCPATH . 'files/media/');
//16A 每日刷卡記錄-匯入範本檔
if (!defined('HTTP_EXAMPLE_FILE')) define('HTTP_EXAMPLE_FILE', HTTP_ROOT . 'files/example_files/');
//16A 每日刷卡記錄-匯入
if (!defined('DIR_UPLOAD_FILES')) define('DIR_UPLOAD_FILES', FCPATH . 'files/upload_files/');
//17G 結業書證管理
if (!defined('HTTP_FIX_FILE')) define('HTTP_FIX_FILE', HTTP_ROOT . 'files/fix_str/'); //造字
if (!defined('DIR_UPLOAD_CERTS')) define('DIR_UPLOAD_CERTS', FCPATH . 'files/certificate/'); //證書底圖
//18A 匯出終身學習時數檔 - 環教時數檔 files/meida/upload_score, 下載整批檔案 files/meida/upload_score_all, 全國教師下載整批檔案 files/meida/extTemp
if (!defined('HTTP_MEDIA')) define('HTTP_MEDIA', HTTP_ROOT . 'files/media/');
//29E 學員上課紀錄-異動表上傳
if (!defined('DIR_MODIFY')) define('DIR_MODIFY', FCPATH . 'files/upload_modify/');
//31A 電視牆設定 files/upload_tv_wall
//9G 班期資訊 files/upload_files/class_info
//31B 卡機輪播設定 files/upload_card_rotation
if (!defined('DIR_UPLOAD_CARD_ROTATION')) define('DIR_UPLOAD_CARD_ROTATION', FCPATH . 'files/upload_card_rotation/');

//2A 講座/助教基本資料及可授課程查詢 - 教師圖像 files/media/data/teacher/

//33 操作文件 \files\txt