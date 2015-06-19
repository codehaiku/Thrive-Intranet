<?php
/**
 * This file contains core functions that are use
 * as help logic in thrive projects component
 *
 * @since  1.0
 * @package Thrive Intranet 
 * @subpackage Projects
 */
if (!defined('ABSPATH')) die();

function thrive_component_id() {
	return apply_filters('thrive_component_id', 'projects');
}

function thrive_component_name() {
	return apply_filters('thrive_component_name', __('Projects', 'thrive'));
}

function thrive_template_dir() {
	return plugin_dir_path(__FILE__) . '../templates';
}

function thrive_include_dir() {
	return plugin_dir_path(__FILE__) . '../includes';
}
?>