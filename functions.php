<?php
/**
 * Includes all the file necessary for Thrive Intranet Plugin
 */
if ( ! defined( 'ABSPATH' ) ) { die(); }

// redirect the user when he/she visit wp-admin or wp-login.php
add_action( 'init', 'thrive_redirect_login' );

// redirect the user after successful logged in attempt
add_filter( 'login_redirect', 'thrive_redirect_user_after_logged_in', 10, 3 );

// handle failed login redirection
add_action( 'wp_login_failed', 'thrive_redirect_login_handle_failure' );

/**
 * Redirect all wp-login.php post 
 * and get request to the user assigned log-in page
 *
 * @return void
 */
function thrive_redirect_login() {

	// Only run this function when on wp-login.php 
	if ( ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) ) ) {
		return;
	}

	// Bypass login if specified.
	$no_redirect = filter_input( INPUT_GET, 'no_redirect', FILTER_VALIDATE_BOOLEAN );

	// Bypass wp-login.php?action=*
	$has_action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );

	$has_error = filter_input( INPUT_GET, 'error', FILTER_SANITIZE_STRING );
	$has_type = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );

	// Set the default to our login page
	$redirect_page = thrive_get_redirect_page_url();

	if ( $has_error && $has_type ) {

		$redirect_to = add_query_arg( array(
				'login' => 'failed',
				'type' => $has_type
			), $redirect_page );

		wp_safe_redirect( esc_url_raw( $redirect_to ) );

		die();
	}

	// Bypass wp-login.php?action=* link.
	if ( $has_action ) {
		return;
	}

	if ( $no_redirect ) {
		return;
	}

	// Check if buddypress activate page.
	if ( function_exists( 'bp_is_activation_page' ) ) {
		if ( bp_is_activation_page() ) {
			return;
		}
	}

	// Check if buddypress registration page.
	if ( function_exists('bp_is_register_page')) {
		if ( bp_is_register_page() ) {
			return;
		}
	}


	// Store for checking if this page equals wp-login.php.
	$curr_paged = basename( $_SERVER['REQUEST_URI'] );

	if ( empty( $redirect_page ) ) {

		return;

	}

	// if user visits wp-admin or wp-login.php, redirect them
	if ( strstr( $curr_paged, 'wp-login.php' ) ) {

		if ( isset( $_GET[ 'interim-login' ] ) ) {
			return;
		}

		// check if there is an action present
		// action might represent user trying to log out
		if ( isset( $_GET['action'] ) ) {

			$action = $_GET['action'];

			if ( 'logout' === $action ) {

				return;

			}
		}

		// Only redirect if there are no incoming post data.
		if ( empty( $_POST ) ) {
			wp_safe_redirect( $redirect_page );
		}

		// Redirect to error page if user left username and password blank
		if ( ! empty( $_POST ) ) {

			if ( empty( $_POST['log'] ) && empty( $_POST['pwd'] ) ) 
			{
				$redirect_to = add_query_arg( array(
						'login' => 'failed',
						'type' => '__blank'
					), $redirect_page );

				wp_safe_redirect( esc_url_raw( $redirect_to ) );
			} 
			elseif ( empty( $_POST['log'] ) && ! empty( $_POST['pwd'] ) && ! empty( $_POST['redirect_to'] ) ) 
			{
				// Username empty
				$redirect_to = add_query_arg( array(
						'login' => 'failed',
						'type' => '__userempty'
					), $redirect_page );

				wp_safe_redirect( esc_url_raw( $redirect_to ) );
			} 
			elseif ( ! empty( $_POST['log'] ) && empty( $_POST['pwd'] ) && ! empty( $_POST['redirect_to'] ) ) 
			{
				// Password empty
				$redirect_to = add_query_arg( array(
						'login' => 'failed',
						'type' => '__passempty'
					), $redirect_page );

				wp_safe_redirect( esc_url_raw( $redirect_to ) );
			} 
			else 
			{
				wp_safe_redirect( $redirect_page );
			}

		}
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

	if ( isset( $user->ID ) ) {

		// check if buddypress is active
		if ( function_exists( 'bp_core_get_user_domain' ) ) {

			return apply_filters( 'thrive_login_redirect', bp_core_get_user_domain( $user->ID ) );

			// otherwise, throw the user into homepage
		} else {

			return apply_filters( 'thrive_login_redirect', home_url() );

		}
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

	// Pull the sign-in page url
	$sign_in_page = wp_login_url();

	$custom_sign_in_page_url = thrive_get_redirect_page_url();

	if ( ! empty( $custom_sign_in_page_url ) ) {

		$sign_in_page = $custom_sign_in_page_url;

	}

	// check that were not on the default login page
	if ( ! empty( $sign_in_page ) && ! strstr( $sign_in_page,'wp-login' ) && ! strstr( $sign_in_page,'wp-admin' ) && $user != null ) {

		// make sure we don't already have a failed login attempt.
		if ( ! strstr( $sign_in_page, '?login=failed' ) ) {

			// Redirect to the login page and append a querystring of login failed.
			$permalink = add_query_arg( array(
	    		'login' => 'failed',
			), $custom_sign_in_page_url );

	  		wp_safe_redirect( esc_url_raw( $permalink ) );

	  		die();

	    } else {

	      	wp_safe_redirect( $sign_in_page );

	      	die();
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

	$selected_login_post_id = intval( get_option( 'thrive_login_page' ) );

	if ( $selected_login_post_id === 0 ) {

		return;

	}

	$login_post = get_post( $selected_login_post_id );

	if ( ! empty( $login_post ) ) {

		return get_permalink( $login_post->ID );

	}

	return false;

}
?>
