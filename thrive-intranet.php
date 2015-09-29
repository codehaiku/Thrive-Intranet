<?php
/**
 * Plugin Name: Thrive - Intranet
 * Description: A helper plugin for 'Thrive WordPress Theme'. Contains all the functions for project management, private pages access, etc.
 * Version: 1.3
 * Author: Dunhakdis
 * Author URI: http://dunhakdis.me
 * Text Domain: thrive
 * License: GPL2
 *
 * Includes all the file necessary for Thrive Intranet Plugin
 *
 * PHP version 5
 *
 * @category  ThriveHelper
 * @package   ThriveIntranet
 * @author    Dunhakdis <noreply@dunhakdis.me>
 * @copyright 2015 - Dunhakdis Software Creatives
 * @license   http://www.php.net/license/3_01.txt  PHP License 3.01
 * @link      http://dunhakdis.me
 * @since     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

define( 'THRIVE_PROJECT_LIMIT', 10 );
define( 'THRIVE_PROJECT_SLUG', 'project' );

require_once plugin_dir_path( __FILE__ ) . 'shortcodes.php';
require_once plugin_dir_path( __FILE__ ) . 'functions.php';
require_once plugin_dir_path( __FILE__ ) . 'private.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/project-post-type.php';
require_once plugin_dir_path( __FILE__ ) . 'core/enqueue.php';

require_once plugin_dir_path( __FILE__ ) . 'install/table.php';

// Setup the tables on activation.
register_activation_hook( __FILE__, 'thrive_install' );

// Include thrive projects transactions.
add_action( 'init', 'thrive_register_transactions' );

// Include thrive projects component.
add_action( 'bp_loaded', 'thrive_register_projects_component' );


/**
 * Register our transactions
 * @return void
 */
function thrive_register_transactions() {

	include_once plugin_dir_path( __FILE__ ) . 'transactions/controller.php';

	return;
}

/**
 * Register our project components
 * @return void
 */
function thrive_register_projects_component() {

	include_once plugin_dir_path( __FILE__ ) .
				 '/includes/project-component.php';
	return;
}

/**
 * Enable GitHub Updater Class
 */
if ( is_admin() ) { 

	include_once plugin_dir_path( __FILE__ ) . '/updater.php';

    $config = array(
        'slug' => plugin_basename(__FILE__), // this is the slug of your plugin
        'proper_folder_name' => 'thrive-intranet', // this is the name of the folder your plugin lives in
        'api_url' => 'https://api.github.com/codehaiku/Thrive-Intranet', // the GitHub API url of your GitHub repo
        'raw_url' => 'https://raw.github.com/codehaiku/Thrive-Intranet/master', // the GitHub raw url of your GitHub repo
        'github_url' => 'https://github.com/codehaiku/Thrive-Intranet', // the GitHub url of your GitHub repo
        'zip_url' => 'https://github.com/codehaiku/Thrive-Intranet/zipball/master', // the zip url of the GitHub repo
        'sslverify' => true, // whether WP should check the validity of the SSL cert when getting an update, see https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/2 and https://github.com/jkudish/WordPress-GitHub-Plugin-Updater/issues/4 for details
        'requires' => '4.0', // which version of WordPress does your plugin require?
        'tested' => '4.0', // which version of WordPress is your plugin tested up to?
        'readme' => 'README.md', // which file to use as the readme for the version number
        'access_token' => '', // Access private repositories by authorizing under Appearance > GitHub Updates when this example plugin is installed
    );

    new WP_GitHub_Updater( $config );
}
?>
