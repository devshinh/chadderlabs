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

$route['default_controller'] = "page";
$route['404_override'] = 'errors/error_404';

// predefined shortcuts
$route['cron'] = "cron";
$route['cron/(:any)?'] = "cron/$1";
$route['page-not-found'] = 'sitemap/page_not_found';
$route['sitemap'] = 'sitemap/index';
$route['sitemap/xml'] = 'sitemap/xml';
$route['contact-us'] = 'contact/index';
$route['contact-us-confirm'] = 'contact/confirm';
$route['sign-up'] = 'newsletter/index';
$route['sign-up-confirm'] = 'newsletter/confirm_signup';
$route['support/coverage'] = 'coverage/index';
$route['support/faq(:any)?'] = "faq$1";


// my-account and user authentication
$route['login'] = 'account/login';
$route['logout'] = 'account/logout';
$route['register'] = 'account/register';
$route['register-confirm'] = 'account/confirm_register';
$route['forgot-password'] = 'account/forgot_password';
$route['my-account/change-email'] = 'account/change_email';
$route['my-account/verify-email(:any)?'] = 'account/verify_email$1';
$route['my-account/user-email'] = 'account/get_email';
$route['my-account/change-password'] = 'account/change_password';
$route['my-account/reset_password(:any)?'] = 'account/reset_password$1';
$route['my-account/activate(:any)?'] = 'account/activate$1';
$route['profile'] = "account/profile";
$route['profile-update'] = "account/edit_profile";
$route['my-account/update-contact(:any)?'] = "account/edit_contact$1";
$route['profile/add_new_contact(:any)?'] = "account/add_new_contact$1";
$route['profile/delete_contact(:any)?'] ="account/delete_contact$1";
//$route['profile(:any)?'] = "account$1";
//$route['profile/refer-colleague-history'] = "account/refer_colleague_history";
$route['public-profile(:any)?'] = "account/public_profile$1";
$route['profile/order_detail(:any)?'] = "account/order_detail$1";

$route['profile-newsletters-edit'] = "account/newsletter_update";

$route['avatar/upload(:any)?'] = "asset/upload_avatar$1";

$route['verification/upload(:any)?'] = "asset/upload_verification$1";
$route['verification/delete/(:any)?'] = "verification/delete/$1";


// shopping cart
//$route['shop/top-up-your-phone-number'] = "shop/airtime";
//$route['shop/no-contract-prepaid-phones'] = 'shop/bundles';
$route['shopping/cart'] = "shop/cart";
//$route['shop/billing'] = "shop/checkout_billing";
$route['shop/shipping'] = "shop/checkout_shipping";
//$route['shop/payment'] = "shop/checkout_payment";
$route['shop/confirm'] = "shop/checkout_confirm";
$route['shop/receipt'] = "shop/receipt";
//$route['shop/add-phone/(:any)'] = "shop/addphone/$1";
$route['shop/(:any)?'] = "shop/$1";


$route['contact/add_contact_from_shipping'] = "contact/add_contact_from_shipping";
//$route['location(:any)?'] = "location/get_location_page$1";

//$route['add_keener(:any)?'] = "badge/badge_keener$1";

//temporary routes
//$route['retailerstorefix(:any)?'] = "account/retailer_store_fix$1";
//$route['eauserrolefix(:any)?'] = "account/ea_user_role_fix$1";
//$route['removepointsfromnonbestbuyusers(:any)?'] = "account/removepointsfromnonbestbuyusers$1";
//$route['fix_screen_name(:any)?'] = "account/fix_screen_name$1";
//$route['add_swagger(:any)?'] = "account/add_swagger$1";
//$route['remove_points(:any)?'] = "account/remove_points";
//$route['fix_quiz_points(:any)?'] = "account/fix_quiz_points";
//$route['add_slug_brands'] = "account/add_slug_brands";
//$route['gamestop_import'] = "account/gamestop_import";

//$route['fix_uk_counties_slug'] = "account/fix_uk_counties_slug";

//$route['sphero_badge'] = "account/check_sphero_badge_test";
//$route['points_fix(:any)?'] = "account/points_fix";

//$route['pdf_test'] = "account/pdf_create";

// override the underscore route setting
$route['ajax(:any)?'] = 'ajax$1';
$route['hotajax(:any)?'] = 'hotajax$1';
$route['(:any)']           = "page/$1";

//$route['language/.+']         = 'language';
//$route['(ajax\_\_.+)']        = '$1';
//$route['(ajax\_.+)']          = 'module_ajax/$1';
//$route['(.+)(\/?.*)']         = "module/module_$1$2";


/* End of file routes.php */
/* Location: ./application/config/routes.php */
