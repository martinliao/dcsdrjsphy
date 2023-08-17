<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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
//defined('HTTP_MEDIA')          OR define('HTTP_MEDIA', HTTP_ROOT . "media/");
