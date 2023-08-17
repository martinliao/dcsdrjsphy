<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
*/
$httpRoot  = "http://".$_SERVER['HTTP_HOST'];
$httpRoot .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
$httpRoot = rtrim($httpRoot, '/'); /** */
$config['base_url'] = $httpRoot;

/*
|--------------------------------------------------------------------------
| Page Title
|--------------------------------------------------------------------------
**/

$config['title']      = '實體班期系統';
$config['title_mini'] = '實';
$config['title_lg']   = '實體';