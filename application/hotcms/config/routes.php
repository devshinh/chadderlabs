<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "dashboard";
$route['404_override'] = '';

// my-account and user authentication
$route['hotcms/login'] = "account/login";
$route['hotcms/logout'] = "account/logout";
$route['hotcms/forgot-password'] = 'account/forgot_password';
$route['hotcms/my-account/change-email'] = 'account/change_email';
$route['hotcms/my-account/verify-email(:any)?'] = 'account/verify_email$1';
$route['hotcms/my-account/user-email'] = 'account/get_email';
$route['hotcms/my-account/change-password'] = 'account/change_password';
$route['hotcms/my-account/profile'] = "account/profile";
$route['hotcms/my-account/update'] = "account/update_profile";
$route['hotcms/my-account(:any)?'] = "account$1";

$route['hotcms/user(:any)?'] = "user$1";
$route['hotcms/site(:any)?'] = "site$1";
$route['hotcms/page(:any)?'] = "page$1";
$route['hotcms/module(:any)?'] = "module$1";
$route['hotcms/role(:any)?'] = "role$1";
$route['hotcms/media-library(:any)?'] = "asset$1";
$route['hotcms/menu(:any)?'] = "menu$1";
$route['hotcms/product(:any)?'] = "product$1";
$route['hotcms/news(:any)?'] = "news$1";
$route['hotcms/quiz(:any)?'] = "quiz$1";
$route['hotcms/organization(:any)?'] = "retailer$1";
$route['hotcms/order(:any)?'] = "order$1";
$route['hotcms/verification(:any)?'] = "verification$1";

$route['hotcms/badge(:any)?'] = "badge$1";

$route['hotcms/training(:any)?'] = "training$1";

$route['hotcms/auction(:any)?'] = "auction$1";

$route['hotcms/contact(:any)?'] = "contact$1";
$route['hotcms/operation_hours(:any)?'] = "operation_hours$1";

$route['hotcms/location(:any)?'] = "location$1";
//$route['hotcms/organization(:any)?'] = "organization$1";
//$route['hotcms/member(:any)?'] = "member$1";
$route['hotcms/dashboard(:any)?'] = "dashboard$1";
$route['hotcms/ajax/(:any)?'] = 'ajax/$1';

$route['hotcms/draw(:any)?'] = "draw$1";

$route['hotcms/target(:any)?'] = "target$1";



$route['hotcms(:any)?'] = "dashboard";

//$route['hotcms/eaimport'] = "news/eaImport";


//$route['language/.+']         = 'language';
//$route['(ajax\_\_.+)']        = '$1';
//$route['(ajax\_.+)']          = 'module_ajax/$1';
//$route['(.+)(\/?.*)']         = "module/module_$1$2";



/* End of file routes.php */
/* Location: ./application/config/routes.php */