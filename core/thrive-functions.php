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

function thrive_render_task() {
	
	require_once(plugin_dir_path(__FILE__) . '../controllers/thrive-project-tasks.php');

	$thrive_tasks = new ThriveProjectTasksController();
	$tasks = $thrive_tasks->renderTickets($id=null);

	if (empty($tasks)) {
		echo '<p class="bp-template-notice error" id="thrive-message">';
			echo __('There are no tasks assigned to this project yet.', 'thrive');
		echo '</p>';
	} else {
		echo '<table class="wp-list-table widefat fixed striped pages" id="thrive-core-functions-render-task">';
		echo '<tr>';
			echo '<th width="70%">'.__('Title', 'thrive').'</th>';
			echo '<th>'.__('Priority', 'thrive').'</th>';
			echo '<th>'.__('Date', 'thrive').'</th>';
		echo '</tr>';
		
		foreach((array)$tasks as $task) {
			
			$row_actions = '<div class="row-actions">';
				$row_actions .= '<span class="edit"><a href="#tasks/edit/'.intval($task->id).'">Edit</a> | </span>';
				$row_actions .= '<span class="complete"><a href="#">Complete</a> | </span>';
				$row_actions .= '<span class="trash"><a data-ticket-id="'.intval($task->id).'" class="thrive-delete-ticket-btn" href="#">Delete</a> </span>';
			$row_actions .= '</div>';
				
			echo '<tr>';
				echo '<td><strong><a class="row-title" href="#">'.stripslashes(esc_html($task->title)).'</a></strong>'.$row_actions.'</td>';
				echo '<td>'.esc_html($task->priority).'</h3></td>';
				echo '<td>'.esc_html(date("Y/m/d", strtotime($task->date_created))).'</h3></td>';

			echo '</tr>';
		}
		echo '</table>';
	}

	return;

}
?>