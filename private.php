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

// check if publitize is enabled inside theme option
// redirect all pages to login page except for selected page
$thrive_publitize_web = intval( get_option( 'thrive_is_public' ) );

if ( $thrive_publitize_web !== 1 ) {

	add_action( 'wp', 'thrive_redirect_to_login' );

}

/**
 * Redirects all the pages except for few selected pages inside
 * the reading settings
 *
 * (Front-end General)
 *
 * @return void
 */
function thrive_redirect_to_login() {

	global $post;

	$login_page_id = intval( get_option( 'thrive_login_page' ) );

	$excluded_page = thrive_get_excluded_page_id_collection();


	// Already escaped inside 'thrive_get_redirect_page_url'.
	$redirect_page = thrive_get_redirect_page_url();


	// Check if redirect page is empty or not.
	if ( empty( $redirect_page ) ) { 
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


	// In case their is no post ID assign a 0 value to
	// $post->ID. This pages applies to custom WordPress pages
	// like BuddyPress Members and Groups.
	if ( empty( $post ) ) 
	{
		$post = new stdclass;
		$post->ID = 0;
	}

	// Check if current page is locked down or not.
	$current_page_id = intval( $post->ID );

	// Check if $current_page_id && $selected_blog_id is equal to each other
	// If that's the case, get the page ID instead of global $post->ID that returns
	// the ID of the first post object inside the loop
	$blog_id = intval( get_option('page_for_posts') );

	if ( is_home() ) {
		if ( $blog_id === $login_page_id ) {
			$current_page_id = $blog_id;
		}
	}
	
	// Only execute the script for non-loggedin visitors.
	if ( ! is_user_logged_in() ) {

		if ( $current_page_id !== $login_page_id ) {

			if ( ! in_array( $current_page_id, $excluded_page ) ) {

				wp_safe_redirect ( add_query_arg(array('_redirected'=>'yes'), $redirect_page ) );
				
				die();

			}

		}
		
	}

	return;

}

/**
 * Helper function to get the ID collection of all
 * the selected pages inside the reading settings
 *
 * @return array the formatted version of pages ID separated by comma
 */
function thrive_get_excluded_page_id_collection() {

	$thrive_public_post = get_option( 'thrive_public_post' );
		$excluded_pages_collection = array();

	if ( ! empty( $thrive_public_post ) ) {
		$excluded_pages_collection = explode( ',', $thrive_public_post );
	}

		// should filter it by integer, spaces will be ignored, other strings
		// will be converted to zero '0'
		return array_filter( array_map( 'intval', $excluded_pages_collection ) );
}

add_action( 'admin_init', 'thrive_settings_api_init' );
/**
 * Register all the settings inside 'Reading' section
 * of WordPress Administration Panel
 *
 * @return void
 */
function thrive_settings_api_init() {

	// new 'Pages Visibility Settings'
		add_settings_section(
			'thrive_setting_section',
			__( 'Pages Visibility Settings', 'dunhakdis' ),
			'thrive_setting_section_callback_function',
			'reading'
		);

		// @get_option('thrive_public_post')
		add_settings_field(
			'thrive_public_post',
			__( 'Display the following pages and posts in public', 'dunhakdis' ),
			'thrive_setting_callback_function',
			'reading',
			'thrive_setting_section'
		);

		// @get_option('thrive_is_public')
		add_settings_field(
			'thrive_is_public',
			__( 'Make my Intranet public', 'dunhakdis' ),
			'thrive_is_public_form',
			'reading',
			'thrive_setting_section'
		);

		// @get_option('thrive_login_page')
		add_settings_field(
			'thrive_login_page',
			__( 'Login Page', 'dunhakdis' ),
			'thrive_login_page_form',
			'reading',
			'thrive_setting_section'
		);

		// register all the callback settings id
		register_setting( 'reading', 'thrive_public_post' );
		register_setting( 'reading', 'thrive_is_public' );
		register_setting( 'reading', 'thrive_login_page' );

}


/**
 * Register a callback function that will handle
 * the 'Pages Visibility Settings' Page
 *
 * @return void
 */
function thrive_setting_section_callback_function() {
	// do nothing
	return;
}

	/**
	 * Callback function for 'thrive_public_post' setting
	 *
	 * @return void
	 */
function thrive_setting_callback_function() {

	echo '<textarea id="thrive_public_post" name="thrive_public_post" rows="5" cols="95">'.esc_attr( trim( get_option( 'thrive_public_post' ) ) ).'</textarea>';
	echo '<p class="description">'.__( 'Enter the IDs of posts and/or pages that you wanted to show in public. You need to separate it by ","(comma), <br>for example: 143,123,213. Alternatively, you can enable public viewing of all of your pages and posts by checking <br>the option below.', 'dunhakdis' ).'</p>';

	return;
}

	/**
	 * Callback function for 'thrive_is_public' setting
	 *
	 * @return void
	 */
function thrive_is_public_form() {

	echo '<label for="thrive_is_public"><input '.checked( 1, get_option( 'thrive_is_public' ), false ).' value="1" name="thrive_is_public" id="thrive_is_public" type="checkbox" class="code" /> Check to make all of your posts and pages public</label>';
	echo '<p class="description">'.__( 'Pages like user profile, members, and groups are still only available to the rightful owner of the profile', 'dunhakdis' ).'</p>';

	return;
}

/**
 * Callback function for 'thrive_login_page' setting
 *
 * @return void
 */
function thrive_login_page_form() {

	$thrive_login_page_id = intval( get_option( 'thrive_login_page' ) );

	wp_dropdown_pages(array(
		'name' => 'thrive_login_page',
		'selected' => $thrive_login_page_id,
		'show_option_none' => __( 'Select Page', 'thrive' ),
	));

	echo '<p class="description">'.__( 'Select a page to use as a login page for your website. You should add "[thrive_login]" shortcode to the selected page<br> to show the login form', 'dunhakdis' ).'</p>';

	return;
}
?>
