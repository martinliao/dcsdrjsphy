<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'dcsdindex';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['Getamd/(:any)'] = "Getamd/css/$1";
$route['Getamd/(:any)/(:any)'] = "Getamd/mod/$1/$2";
$route['Getamd/(:any)/(:any)/(:any)'] = "Getamd/get/$1/$2/$3";
$route['Javascript/lib/(:any)/(:any)'] = "Javascript/lib/$1/$2";
$route['Javascript/lib/jquery/(:any)/(:any)'] = "Javascript/jquery/$1/$2/$3";

$route['importdatabase'] = 'Admin/importdatabase';
// SmartyaACL route
$route['login']    = 'Auth/login';
$route['logout']   = 'Auth/logout';
$route['admin'] = 'Admin/index';
$route['admin/login'] = 'AuthAdmin/index';
$route['admin/logout'] = 'AuthAdmin/logout';
$route['register'] = 'Auth/register';
$route['account']  = 'welcome/account';
//Modules
$route['admin/modules'] = 'Admin/modules';
$route['admin/modules/create'] = 'Admin/module_create';
$route['admin/modules/edit/(:num)'] = 'Admin/module_edit/$1';
$route['admin/modules/delete/(:num)'] = 'Admin/module_delete/$1';
//Roles
$route['admin/roles'] = 'Admin/roles';
$route['admin/roles/create'] = 'Admin/role_create';
$route['admin/roles/edit/(:num)'] = 'Admin/role_edit/$1';
$route['admin/roles/delete/(:num)'] = 'Admin/role_delete/$1';
//Admins
$route['admin/admins'] = 'Admin/admins';
$route['admin/admins/create'] = 'Admin/admin_create';
$route['admin/admins/edit/(:num)'] = 'Admin/admin_edit/$1';
$route['admin/admins/delete/(:num)'] = 'Admin/admin_delete/$1';
//Users
$route['admin/users'] = 'Admin/users';
$route['admin/users/create'] = 'Admin/user_create';
$route['admin/users/edit/(:num)'] = 'Admin/user_edit/$1';
$route['admin/users/delete/(:num)'] = 'Admin/user_delete/$1';
// Custom Routes
$route['/'] = 'general/index';

// Ajax API routes
$route['api'] = 'api/ajax/index';
$route['api/get?(:any)'] = 'api/ajax/get/$1';
$route['api/post'] = 'api/ajax/post';

// Catch all default for direct access to controllers
$route['(:any)/(:any)'] = '$1/$2';
