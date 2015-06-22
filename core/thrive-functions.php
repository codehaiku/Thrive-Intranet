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

function thrive_render_task($echo = true, $page = 1, $limit = 10) {
	
	if (!$echo) { ob_start(); }

	require_once(plugin_dir_path(__FILE__) . '../controllers/thrive-project-tasks.php');

	$thrive_tasks = new ThriveProjectTasksController();
	$tasks = $thrive_tasks->renderTasks($id=null, $page, $limit);
	$stats = $tasks['stats'];
	$tasks = $tasks['results'];
	

	if (empty($tasks)) {
		
		echo '<p class="bp-template-notice error" id="thrive-message">';
			echo __('There are no tasks assigned to this project yet.', 'thrive');
		echo '</p>';

	} else {

		echo '<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
			<option value="-1" selected="selected">Filter Tickets</option>
				<option value="trash">Normal Priority</option>
				<option value="trash">High Priority</option>
				<option value="trash">Critical</option>
				<option value="edit" class="hide-if-no-js">Completed</option>

			</select>
			<input type="submit" id="doaction" class="button action" value="Apply">
		</div>';

		echo '<p class="search-box">
	<label class="screen-reader-text" for="post-search-input">Search Tasks:</label>
	<input type="search" id="post-search-input" name="s" value="">
	<input type="submit" id="search-submit" class="button" value="Search Tasks"></p><br/><br/>';

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
				echo '<td><strong><a class="row-title" href="#tasks/edit/'.intval($task->id).'">'.stripslashes(esc_html($task->title)).'</a></strong>'.$row_actions.'</td>';
				echo '<td>'.esc_html($task->priority).'</h3></td>';
				echo '<td>'.esc_html(date("Y/m/d", strtotime($task->date_created))).'</h3></td>';

			echo '</tr>';
		}
		echo '</table>';

		$total      = intval($stats['total']);
		$perpage    = intval($stats['perpage']);
		$total_page = intval($stats['total_page']);

		echo '<div class="tablenav"><div class="tablenav-pages">';
		echo '<span class="displaying-num">'.sprintf(_n('%s task', '%s tasks', $total, 'thrive'),$total).'</span>';
		
		if ($total_page >= 1) {
			echo '<span id="trive-task-paging" class="pagination-links">';
				echo '<a class="first-page disabled" title="'.__('Go to the first page', 'thrive').'" href="#">«</a>';
				echo '<a class="prev-page disabled" title="'.__('Go to the previous page', 'thrive').'" href="#">‹</a>';

						echo '<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">'.__('Select Page', 'thrive').'</label>';
						echo '<input class="current-page" id="current-page-selector" type="text" maxlength="'.strlen($total_page).'" size="'.strlen($total_page).'"value="1">';
						echo ' of <span class="total-pages">'.$total_page.'</span></span>';

				echo '<a class="next-page" title="'.__('Go to the next page', 'thrive').'" href="#">›</a>';
				echo '<a class="last-page" title="'.__('Go to the last page', 'trive').'" href="">»</a></span>';
			echo '</span>';
		}

		echo '</div></div><!--.tablenav--><!--.tablenav-pages-->';
	}

	if (!$echo) { 
		return ob_get_clean(); 
	} else {
		return;
	}
}
?>