<?php
/**
 * Includes all the file necessary for Thrive Intranet Plugin
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to version 3.01 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_01.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * @category   ThriveHelper
 * @package    Thrive
 * @author     Dunhakdis <http://dunhakdis.me/say-hello>
 * @copyright  2015 - Dunhakdis Software Creatives
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @since      1.0
 */
if (!defined('ABSPATH')) die();

// redirect the user when he/she visit wp-admin or wp-login.php
add_action('init', 'thrive_redirect_login');
// redirect the user after successful logged in attempt
add_filter('login_redirect', 'thrive_redirect_user_after_logged_in', 10, 3);
// handle failed login redirection
add_action('wp_login_failed', 'thrive_redirect_login_handle_failure'); 

/**
 * Redirect all wp-login.php request to 
 * the user assigned log-in page
 * 
 * @return void
 */
function thrive_redirect_login() {

	// Bypass login if specified
	$no_redirect = filter_input( INPUT_GET, 'no_redirect', FILTER_VALIDATE_BOOLEAN );

	if ( $no_redirect ) {
			return;
		}

 	// Store for checking if this page equals wp-login.php
 	$curr_paged = basename( $_SERVER['REQUEST_URI'] );

 	// Set the default to our login page
 	$redirect_page = thrive_get_redirect_page_url();

 	// if user visits wp-admin or wp-login.php, redirect them
 	if ( strstr( $curr_paged, 'wp-login.php' ) ) {

 		if ( isset( $_GET[ 'interim-login' ] ) ) {
 			return;
 		}
 		
 		// check if there is an action present
 		// action might represent user trying to log out
 	 	
 	 	if ( isset( $_GET['action'] ) ) {
 	 		
 	 		$action = $_GET['action'];

 	 		if ( "logout" === $action ) {
 	 			return;
 	 		}
 	 	}

 		wp_safe_redirect($redirect_page);
 	}

 	return;
}

/**
 * Redirects the user to homepage after logged-in
 * if buddypress is present, then it will redirect the
 * user to it's profile page
 * 
 * @param  string $redirect_to 'login_redirect' filter callback argument
 * @param  object $request     'login_redirect' filter callback argument
 * @param  object $user        'login_redirect' filter callback argument
 * @return string              The final redirection url
 */
function thrive_redirect_user_after_logged_in( $redirect_to, $request, $user ) {
	
	global $user;

	if ( empty( $user ) ) { 
		return $redirect_to;
	}

	// check if buddypress is active
	if ( function_exists( 'bp_core_get_user_domain' ) ) {
		return apply_filters( 'thrive_login_redirect', bp_core_get_user_domain( $user->ID ) );
	// otherwise, throw the user into homepage	
	} else {
		return apply_filters( 'thrive_login_redirect', home_url() );
	}
	// double edge sword
	return $redirect_to;
}

/**
 * Handles the failure login attempt from customized login page
 * 
 * @param  object $user WordPress callback function
 * @return void
 */
function thrive_redirect_login_handle_failure( $user ) {
  	// check what page the login attempt is coming from
  	$referrer = home_url() . '/login';
  	// check that were not on the default login page
	if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') && $user!=null ) {
		// make sure we don't already have a failed login attempt
		if ( !strstr($referrer, '?login=failed' )) {
			// Redirect to the login page and append a querystring of login failed
	    	wp_safe_redirect($referrer . '?login=failed');
	    } else {
	      	wp_safe_redirect($referrer);
	    }
	    return;
	}

	return;
}

/**
 * Helper function to return the user selected page
 * inside the 'Login' under 'Reading' settings
 * 
 * @return void
 */
function thrive_get_redirect_page_url() {

	$login_page_id = intval( get_option( 'thrive_login_page' ) );

	if ( $login_page_id === 0 )  { 
		return;
	}
	
	$login_post = get_post( $login_page_id );

	if ( !empty( $login_post ) ) {

		$slug = $login_post->post_name;

		$login_page_name = apply_filters( 'thrive_login_page_slug', $slug );

 		$redirect_page = esc_url( site_url() . '/' . esc_attr( $login_page_name ) );

 		return $redirect_page;

 	}

 	return false;
 	
}
?>