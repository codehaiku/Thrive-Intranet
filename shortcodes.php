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

if ( ! defined( 'ABSPATH' ) ) { die(); }
/**
 * Callback function to register 'thrive_login'
 * shortcode in order to display the login form
 * inside the page using 'thrive_login' shortcode
 *
 * @param  array $atts shortcode callback attributes
 * @return string 	output_buffer
 */
function thrive_wp_login( $atts ) {

	// begin output buffering
	ob_start();

	$args = array(
	    'echo'           => true,
	    'form_id'        => 'loginform',
	    'label_username' => __( 'Username', 'thrive' ),
	    'label_password' => __( 'Password', 'thrive' ),
	    'label_remember' => __( 'Remember Me', 'thrive' ),
	    'label_log_in'   => __( 'Log In', 'thrive' ),
	    'id_username'    => 'user_login',
	    'id_password'    => 'user_pass',
	    'id_remember'    => 'rememberme',
	    'id_submit'      => 'wp-submit',
	    'remember'       => true,
	    'value_username' => '',
	    'value_remember' => false,
	);

	$error_login_message = '';
	$message_types = array();

	if ( isset( $_GET['login'] ) ) {

		if ( 'failed' === $_GET['login'] ) {

			if ( isset( $_GET['type'] ) ) {

				$message_types = array(

					'default' => array(
							'message' => __('Error: There was an error trying to sign-in to your account. Make sure your credentials are correct', 'thrive')
						),
					'__blank' => array(
							'message' => __( 'Required: Username and Password cannot not be empty.', 'thrive' )
						),
					'__userempty' => array(
							'message' => __( 'Required: Username cannot not be empty.', 'thrive' )
						),
					'__passempty' => array(
							'message' => __( 'Required: Password cannot not be empty.', 'thrive' )
						),
					'fb_invalid_email' => array(
							'message' => __( 'Facebook email is invalid or is not verified.', 'thrive' )
						),
					'fb_error' => array(
							'message' => __( 'Facebook Application Error. Misconfig or App is rejected.', 'thrive' )
						),
					'app_not_live' => array(
							'message' => __( 'Unable to fetch your Facebook profile.', 'thrive' )
						),
					'gears_username_or_email_exists' => array(
							'message' => __('Username or e-mail already exists', 'thrive')
						),
					'gp_error_authentication' => array(
							'message' => __( 'Google Plus Authentication Error. Invalid Client ID or Secret.', 'thrive' )
						)
				);
				
				$message = $message_types['default']['message'];

				if ( array_key_exists ( $_GET['type'], $message_types ) ) {

					$message = $message_types[ $_GET['type'] ]['message'];

				}

				$error_login_message = '<div id="message" class="error">'. esc_html( $message ) .'</div>';

			} else {

				$error_login_message = '<div id="message" class="error">'.__( 'Error: Invalid username and password combination.', 'thrive' ).'</div>';

			}
		}
	}

	if ( isset( $_GET['_redirected'] ) ) {
		$error_login_message = '<div id="message" class="success">'.__( 'Oops! Looks like you need to login in order to view the page.', 'thrive' ).'</div>';
	}
		
	?>
	<div class="mg-top-35 mg-bottom-35 thrive-login-form">
		<div class="thrive-login-form-form">
			<div class="thrive-login-form__actions">
				<h3>
					<?php _e('Account Sign-in', 'thrive'); ?>
				</h3>	
				<?php do_action( 'gears_login_form' ); ?>
			</div>
			<div class="thrive-login-form-message">
				<?php echo $error_login_message; ?>
			</div>
			<div class="thrive-login-form__form">
				<?php echo wp_login_form( $args ); ?>
			</div>
		</div>
	</div>
	<script>
	jQuery(document).ready(function($){
		"use strict";
		$('.thrive-login-form__form p > input').focusin(function(){
			$(this).prev('label').addClass('inactive');
		}).focusout(function(){
			if ( $(this).val().length < 1 ) {
				$(this).prev('label').removeClass('inactive');
			} 
		});
	});
	</script>
	<?php
			
	return ob_get_clean();

}

add_action( 'login_form_middle', 'thrive_add_lost_password_link' );

function thrive_add_lost_password_link() {

	return '<p class="thrive-login-lost-password"><a href="'.esc_url( wp_lostpassword_url( $redirect = "" ) ).'">' . __('Forgot Password', 'thrive') . '</a></p>';

}

/**
 * callback function to 'init' hook to register
 * @return  void
 */
function thrive_register_shortcode() {
	
	add_shortcode( 'thrive_login', 'thrive_wp_login' );

	return;
}

add_action( 'init', 'thrive_register_shortcode' );
?>
