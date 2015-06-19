<?php
/**
 * Plugin Name: Thrive - Digital Workplace
 * Description: Thrive is the best way to build your company social network on top of WordPress - The Software that we all love. With features like Events, File Sharing, Wiki, Staff Directory, you will be able to create an atmosphere for your workforce where you can easily discuss and collaborate goals, plans, and the next milestone for your company. It’s for smart people with great dreams.  
 * Version: 1.0
 * Author: Dunhakdis
 * Author URI: http://dunhakdis.me
 * Text Domain: thrive
 * License: PHP License 3.01
 */
#==================
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

require_once(plugin_dir_path(__FILE__) . 'shortcodes.php');
require_once(plugin_dir_path(__FILE__) . 'functions.php');
require_once(plugin_dir_path(__FILE__) . 'locked-down-page.php');
require_once(plugin_dir_path(__FILE__) . 'includes/thrive-projects-post.php');
/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
function thrive_register_projects_component() {
	require_once(plugin_dir_path(__FILE__) . '/includes/thrive-projects-component.php' );
}


function thrive_component_id() {
	return 'projects';
}

function thrive_component_name() {
	return __('Projects', 'buddypress');
}

function thrive_template_dir() {
	return plugin_dir_path(__FILE__) . 'templates';
}

function thrive_include_dir() {
	return plugin_dir_path(__FILE__) . 'includes';
}

add_action('bp_loaded', 'thrive_register_projects_component' );

?>