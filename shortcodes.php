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
function thrive_wp_login($atts) {

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

	if ( isset( $_GET['login'] ) ) {

		if ( 'failed' === $_GET['login'] ) {

			if ( isset( $_GET['type'] ) ) {

				if ( $_GET['type'] === '__blank' ) {

					$error_login_message = '<div id="message" class="error">'.__( 'Required: Username and Password cannot not be empty.', 'thrive' ).'</div>';

				} elseif ( $_GET['type'] === '__userempty' ) {

					$error_login_message = '<div id="message" class="error">'.__( 'Required: Username cannot not be empty.', 'thrive' ).'</div>';

				} elseif ( $_GET['type'] === '__passempty' ) {

					$error_login_message = '<div id="message" class="error">'.__( 'Required: Password cannot not be empty.', 'thrive' ).'</div>';

				} else {

					$error_login_message = '<div id="message" class="error">'.__( 'Error: There was an error trying to sign-in to your account. Make sure your credentials are correct.', 'thrive' ).'</div>';

				}
			} else {

				$error_login_message = '<div id="message" class="error">'.__( 'Error: Invalid username and password combination.', 'thrive' ).'</div>';

			}
		}
	}
		echo $error_login_message;
		echo '<div class="mg-top-35 mg-bottom-35">';
				wp_login_form( $args );
		echo '</div>';

		// finally, collect all the buffered output
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
