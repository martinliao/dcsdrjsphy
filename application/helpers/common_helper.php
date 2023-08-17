<?php
function isDate($str){
    if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $str)){
        return false;
    }
    $__y = substr($str, 0, 4);
    $__m = substr($str, 5, 2);
    $__d = substr($str, 8, 2);
    return checkdate($__m, $__d, $__y);
}

function jsMsg($msg, $url=NULL) {
	echo '<script>';
	echo '	alert("'. $msg .'");';
	if ($url) {
		echo 'location.href="'. $url .'";';
	}
	echo '</script>';
	exit;
}
function jd($var, $dead = false) {
	echo '<pre style="text-align:left;">' . "\n";
	print_r($var);
	echo "\n</pre>\n";
	if($dead)
		exit;
}

function jdd($v, $isVarExport = FALSE) {
	$_bt = debug_backtrace();
	$bt0 = & $_bt[0];
	jd("{$bt0['file']}: {$bt0['line']}");
	if ($isVarExport) {
		jd(var_export($v, 1));
	} else {
		jd($v);
	}
}

function jddlog($v, $isVarExport = FALSE) {
	$_bt = debug_backtrace();
	$bt0 = & $_bt[0];

	$out = "{$bt0['file']}: {$bt0['line']}\n";
	if ($isVarExport) {
		$out .= var_export($v, 1);
	} else {
		$out .= print_r($v, 1);
	}

	error_log($out);
}

function jt($msg = '') {
	echo microtime(true);
	echo " {$msg}<br />\n";
}

function cutContent($str, $num){
	$str = strip_tags($str);
	$rs = mb_substr($str, 0, $num, 'UTF-8');

	if (strlen($str) > strlen($rs)) {
		$rs .= '...';
	}

	return $rs;
}

function generatorRandom($lenght = 6, $word = NULL)
{
	if ( ! $word) {
		// remove o,0,1,l
		// $word = 'abcdefghijkmnpqrstuvwxyz!@#$%^&*()-=ABCDEFGHIJKLMNPQRSTUVWXYZ<>;{}[]23456789';
		$word = 'abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ23456789';
	}

	$len = strlen($word);

	$password = '';
	for ($i = 0; $i < $lenght; $i++) {
		$password .= $word[crypto_rand(1,9999) % $len];
	}

	return $password;
}

function getClentIP() {
	if (!empty($_SERVER["HTTP_CLIENT_IP"])){
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}elseif(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
	}else{
		$ip = $_SERVER["REMOTE_ADDR"];
	}

	return $ip;
}

function checkDevice(){
    $ua=$_SERVER['HTTP_USER_AGENT'];
    $iphone = strstr(strtolower($ua), 'mobile'); //Search for 'mobile' in user-agent (iPhone have that)
    $android = strstr(strtolower($ua), 'android'); //Search for 'android' in user-agent
    $windowsPhone = strstr(strtolower($ua), 'phone'); //Search for 'phone' in user-agent (Windows Phone uses that)

    function androidTablet($ua){ //Find out if it is a tablet
        if(strstr(strtolower($ua), 'android') ){//Search for android in user-agent
            if(!strstr(strtolower($ua), 'mobile')){ //If there is no ''mobile' in user-agent (Android have that on their phones, but not tablets)
                return true;
            }
        }
    }
    $androidTablet = androidTablet($ua); //Do androidTablet function
    $ipad = strstr(strtolower($ua), 'ipad'); //Search for iPad in user-agent

    if($androidTablet || $ipad){ //If it's a tablet (iPad / Android)
        return 'tablet';
    }
    elseif($iphone && !$ipad || $android && !$androidTablet || $windowsPhone){ //If it's a phone and NOT a tablet
        return 'mobile';
    }
    else{ //If it's not a mobile device
        return 'desktop';
    }
}
function send_mail($recipient, $subject, $message, $from=NULL) {
	$subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=utf-8\r\n";
	if ($from) {
		$headers .= "From: {$from}\r\n";
	}
    if (is_array($recipient)){
        foreach ($recipient as $to) {
            mail($to, $subject, $message, $headers);
        }
    } else {
        return mail($recipient, $subject, $message, $headers);
    }
}
function curlGet($url, $data=array())
{
    $url .= '?' . http_build_query($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    
    $result = json_decode(curl_exec($ch));
    curl_close($ch);

    return $result;
}

function curlPost($url=NULL, $params=array()) {
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => 0,
        CURLOPT_VERBOSE => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_SSL_VERIFYHOST=>FALSE,
        CURLOPT_SSL_VERIFYPEER=>FALSE,
        CURLOPT_POST => TRUE,
        CURLOPT_POSTFIELDS => http_build_query($params),
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

function curlPostJson($url=NULL, $params=array()) {
    // $params = json_encode($params);
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => 0,
        CURLOPT_VERBOSE => 0,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_SSL_VERIFYHOST=>FALSE,
        CURLOPT_SSL_VERIFYPEER=>FALSE,
        CURLOPT_POST => TRUE,
        CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
        CURLOPT_POSTFIELDS => $params,
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    // $result = json_decode(curl_exec($ch));
    curl_close($ch);

    return $result;
}

function getAgo($date) {
    $time = time() - strtotime($date);
    $days = 0;
    $hours = 0;
    $minutes = floor($time / 60);

    if ($minutes >=60) {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
    }
    if ($hours >= 24) {
        $days = floor($hours / 24);
        $hours = $hours % 24;
    }

    $ago = '';
    if ($days > 0) {
        $ago .= $days.'天 ';
    }

    if ($hours > 0) {
        $ago .= $hours.'時 ';
    }

    if ($minutes > 0) {
        $ago .= $minutes.'分 ';
    }

    if ($ago != '') {
        $ago .= '前';
    }

    return $ago;
}

function std_class_object_to_array($stdclassobject)
{
    $_array = is_object($stdclassobject) ? get_object_vars($stdclassobject) : $stdclassobject;
    $array = array();

    foreach ($_array as $key => $value) {
        $value = (is_array($value) || is_object($value)) ? std_class_object_to_array($value) : $value;
        $array[$key] = $value;
    }

    return $array;
}


function dd($data, $die = true){
    echo "<pre>"; print_r($data);
    if ($die) die;
}

function get_chinese_weekday($datetime)
{
    $weekday = date('w', strtotime($datetime));
    return ['日', '一', '二', '三', '四', '五', '六'][$weekday];
}

function hiddenName($name) {

    $lenName = mb_strlen($name, 'UTf-8');
    if ($lenName >= 3) {
        $name = mb_substr($name, 0, 1, 'UTf-8') . str_repeat ('O', $lenName -2) . mb_substr($name, $lenName-1, 1, 'UTf-8');
    } else if ($lenName === 2) {
        $name = 'O' . mb_substr($name, 1, 1, 'UTf-8');
    }else {
        $name = '';
    }
    return $name;
}

function getLastPageQuery()
{
    if (empty($_GET['lastPageQuery'])) return '';
    $lastPageQuery = "lastPageQuery=".urlencode($_GET['lastPageQuery']);
    return $lastPageQuery;
}

function createLastPageQuery()
{
    if (empty($_SERVER['QUERY_STRING'])) return '';
    return "lastPageQuery=".urlencode($_SERVER['QUERY_STRING']);
}

if (!function_exists('xss_clean'))
{
    function xss_clean($data)
    {
        // Fix &entity\n;
        $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
    
        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
    
        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);
    
        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);
    
        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
    
        do
        {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        }
        while ($old_data !== $data);
    
        // we are done...
        return $data;
    }    
}

if (!function_exists('crypto_rand'))
{
    function crypto_rand($min,$max,$pedantic=True) {
        $diff = $max - $min;
        if ($diff <= 0) return $min; // not so random...
        $range = $diff + 1; // because $max is inclusive
        $bits = ceil(log(($range),2));
        $bytes = ceil($bits/8.0);
        $bits_max = 1 << $bits;
        // e.g. if $range = 3000 (bin: 101110111000)
        //  +--------+--------+
        //  |....1011|10111000|
        //  +--------+--------+
        //  bits=12, bytes=2, bits_max=2^12=4096
        $num = 0;
        do {
            $num = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes))) % $bits_max;
            if ($num >= $range) {
                if ($pedantic) continue; // start over instead of accepting bias
                // else
                $num = $num % $range;  // to hell with security
            }
            break;
        } while (True);  // because goto attracts velociraptors
        return $num + $min;
    }
}

if (!function_exists('filterQueryString')){
    function filterQueryString($queryString)
    {
        parse_str($queryString, $array);
        $filterQueryString = "";
        foreach($array as $key => $value){
            if ($filterQueryString == ""){
                $filterQueryString = htmlspecialchars(urlencode($key), ENT_HTML5|ENT_QUOTES).'='.htmlspecialchars(urlencode($value), ENT_HTML5|ENT_QUOTES);
            }else{
                $filterQueryString .= '&'.htmlspecialchars(urlencode($key), ENT_HTML5|ENT_QUOTES).'='.htmlspecialchars(urlencode($value), ENT_HTML5|ENT_QUOTES);
            }
        }   
        return $filterQueryString;
    }
}

/*
    檢查副檔名
*/
if (!function_exists('fileExtensionCheck')){
    function fileExtensionCheck($filename, $allowType){
        if (is_array($filename)){
            foreach($filename as $name){
                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                if (!in_array($extension, $allowType)){
                    return false;
                }
            }
            return true;
        }else{
            $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            return in_array($extension, $allowType);            
        }
    }
}