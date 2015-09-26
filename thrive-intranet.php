<?php
/**
 * Plugin Name: Thrive - Digital Workplace
 * Description: Easily manage your projects and tasks using this plugin. Has the ability to control the user access to website as well. Perfect for Intranet/Extranet websites.
 * Version: 1.0
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
?>
