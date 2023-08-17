<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = array(APPPATH.'third_party/SmartyAcl');

$autoload['libraries'] = array('database', 'form_validation', 'pagination', 'session');

$autoload['drivers'] = array();

$autoload['helper'] = array('array', 'language', 'menu', 'url');

$autoload['config'] = array();

$autoload['language'] = array();

$autoload['model'] = array();
