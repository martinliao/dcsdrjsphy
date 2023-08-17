// Custom Routes
$route['/'] = 'general/index';

// Ajax API routes
$route['api'] = 'api/ajax/index';
$route['api/get?(:any)'] = 'api/ajax/get/$1';
$route['api/post'] = 'api/ajax/post';

// Catch all default for direct access to controllers
$route['(:any)/(:any)'] = '$1/$2';
$route['translate_uri_dashes'] = FALSE;
