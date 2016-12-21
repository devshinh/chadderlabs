<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - English
* 
* Author: Ben Edmunds
* 		  ben.edmunds@gmail.com
*         @benedmunds
* 
* Location: http://github.com/benedmunds/ion_auth/
*          
* Created:  03.14.2010 
* 
* Description:  English language file for Ion Auth messages and errors
* 
*/

// Account Creation
$lang['account_creation_successful'] 	  	 = 'Member account successfully created.';
$lang['account_creation_unsuccessful'] 	 	 = 'Unable to create member account.';
$lang['account_creation_duplicate_email'] 	 = 'Email already used or invalid.';
$lang['account_creation_duplicate_username'] 	 = 'This email address is already registered.';

// Password
$lang['password_change_successful'] 	 	 = 'Password changed successfully. Please check your email for the new password.';
$lang['password_change_unsuccessful'] 	  	 = 'Unable to change password.';
$lang['forgot_password_successful'] 	 	 = 'An email has been sent to your email address. Please follow the link in the email to reset your password.';
$lang['forgot_password_unsuccessful'] 	 	 = 'Unable to reset password.';

// Activation
$lang['activate_successful'] 		  	 = 'Your account has been activated.';
$lang['activate_unsuccessful'] 		 	 = 'Unable to activate account. The activation link can only work once and if you see this error message, it is possible that your account is already activated. In this case please proceed to the login page.';
$lang['deactivate_successful'] 		  	 = 'Account de-activated.';
$lang['deactivate_unsuccessful'] 	  	 = 'Unable to de-activate account.';
$lang['activation_email_successful'] 	  	 = 'An activation email has been sent to your email address.';
$lang['activation_email_unsuccessful']   	 = 'Unable to send activation email.';

// Login / Logout
$lang['login_successful'] 		  	 = 'Logged in successfully.';
$lang['login_unsuccessful'] 		  	 = 'Invalid login.';
$lang['logout_successful'] 		 	 = 'Logged out successfully.';
  
// Account Changes
$lang['update_successful'] 		 	 = 'Account information successfully updated.';
$lang['update_unsuccessful'] 		 	 = 'Unable to update account information.';
$lang['delete_successful'] 		 	 = 'User deleted.';
$lang['delete_unsuccessful'] 		 	 = 'Unable to delete user.';

$lang['user_error_username']       = 'The username you selected is already in use.';
$lang['user_error_email']         = 'The email address you entered is already in use.';

?>
