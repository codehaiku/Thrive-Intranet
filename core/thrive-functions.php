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

/**
 * Returns the thrive component id or slug
 * @return string the thrive component id or slug
 */
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

/**
 * Display a select field with list of available priorities
 * @param  integer $default     the default priority
 * @param  string  $select_name the name of the select field
 * @param  string  $select_id   the id of the select field
 * @return void              
 */
function thrive_task_priority_select($default = 1, $select_name = 'thrive_task_priority', $select_id = 'thrive-task-priority-select') {

	require_once(plugin_dir_path(__FILE__) . '../controllers/thrive-project-tasks.php');
	
	$thrive_tasks = new ThriveProjectTasksController();

	$priorities = $thrive_tasks->getPriorityCollection();

	echo '<select name="'.esc_attr($select_name).'" id="'.esc_attr($select_id).'" class="thrive-task-select">';
	
	foreach($priorities as $priority_id => $priority_label) {
		
		$selected = (intval($priority_id) === $default) ? 'selected': '';

		echo '<option '.esc_html($selected).' value="'.esc_attr($priority_id).'">'.esc_html($priority_label).'</option>';
	}

	echo '</select>';

	return;
}

function thrive_task_filters() {
	?>
	<div id="thrive-tasks-filter">
		<div class="alignleft actions bulkactions">
			<label for="bulk-action-selector-top" class="screen-reader-text"><?php _e('Select bulk action', 'thrive'); ?></label>
				<select name="action" id="thrive-task-filter-select">
					<option value="-1" selected="selected"><?php _e('Show All', 'thrive'); ?></option>
					<option value="1"><?php _e('Normal Priority', 'thrive'); ?></option>
					<option value="2"><?php _e('High Priority', 'thrive'); ?></option>
					<option value="3"><?php _e('Critical Priority', 'thrive'); ?></option>
				</select>
		</div><!--.alignleft actions bulkactions-->

		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input">
				<?php _e('Search Tasks:', 'thrive'); ?>
			</label>
			<input type="search" id="thrive-task-search-field" name="thrive-task-search" value="">
			<input type="button" id="thrive-task-search-submit" class="button" value="<?php _e('Search', 'thrive'); ?>">
		</p><!--.search box-->

	</div><!--#thrive-task-filter-->
	<?php
}
/**
 * thrive_render_task($echo = true, $page = 1, $limit = 10)
 * 
 * Renders a table that enables admin to manage
 * tickets under a project. Only use this function
 * when calling inside the administration area
 * 
 * @param  boolean $echo  option to show or store the task inside the variable
 * @param  integer $page  sets the current page of tasks
 * @param  integer $limit limits the number of task displayed
 * @return void if $echo is set to true other wise returns the constructed markup for tasks
 */
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
		
		// display the filters (already echoed inside the function)
		thrive_task_filters();

		echo '<div id="thrive-task-list-canvas">';	  
		echo '<table class="wp-list-table widefat fixed striped pages" id="thrive-core-functions-render-task">';
		echo '<tr>';
			echo '<th width="70%">'.__('Title', 'thrive').'</th>';
			echo '<th>'.__('Priority', 'thrive').'</th>';
			echo '<th>'.__('Date', 'thrive').'</th>';
		echo '</tr>';
		
		foreach((array)$tasks as $task) {
			
			$priority_label = $thrive_tasks->getPriority($task->priority);

			$row_actions = '<div class="row-actions">';
				$row_actions .= '<span class="edit"><a href="#tasks/edit/'.intval($task->id).'">Edit</a> | </span>';
				$row_actions .= '<span class="complete"><a href="#">Complete</a> | </span>';
				$row_actions .= '<span class="trash"><a data-ticket-id="'.intval($task->id).'" class="thrive-delete-ticket-btn" href="#">Delete</a> </span>';
			$row_actions .= '</div>';
				
			echo '<tr class='.esc_attr(sanitize_title($priority_label)).'>';
				echo '<td><strong><a class="row-title" href="#tasks/edit/'.intval($task->id).'">'.stripslashes(esc_html($task->title)).'</a></strong>'.$row_actions.'</td>';
				echo '<td>'.esc_html($priority_label).'</h3></td>';
				echo '<td>'.esc_html(date("Y/m/d", strtotime($task->date_created))).'</h3></td>';

			echo '</tr>';
		}
		echo '</table>';

		$total      = intval($stats['total']);
		$perpage    = intval($stats['perpage']);
		$total_page = intval($stats['total_page']);
		$currpage   = intval($stats['current_page']);
		$min_page	= intval($stats['min_page']);
		$max_page   = intval($stats['max_page']);

		echo '<div class="tablenav"><div class="tablenav-pages">';
		echo '<span class="displaying-num">'.sprintf(_n('%s task', '%s tasks', $total, 'thrive'),$total).'</span>';
		
		if ($total_page >= 1) {
			echo '<span id="trive-task-paging" class="pagination-links">';
				echo '<a class="first-page disabled" title="'.__('Go to the first page', 'thrive').'" href="#tasks/page/'.$min_page.'">«</a>';
				echo '<a class="prev-page disabled" title="'.__('Go to the previous page', 'thrive').'" href="#">‹</a>';

						echo '<span class="paging-input"><label for="thrive-task-current-page-selector" class="screen-reader-text">'.__('Select Page', 'thrive').'</label>';
						echo '<input readonly class="current-page" id="thrive-task-current-page-selector" type="text" maxlength="'.strlen($total_page).'" size="'.strlen($total_page).'"value="'.intval($currpage).'">';
						echo ' of <span class="total-pages">'.$total_page.'</span></span>';

				echo '<a class="next-page" title="'.__('Go to the next page', 'thrive').'" href="#">›</a>';
				echo '<a class="last-page" title="'.__('Go to the last page', 'trive').'" href="#tasks/page/'.$max_page.'">»</a></span>';
			echo '</span>';
		}

		echo '</div></div><!--.tablenav--><!--.tablenav-pages-->';
		echo '</div><!--#thrive-task-list-canvas-->';
	}

	if (!$echo) { 
		return ob_get_clean(); 
	} else {
		return;
	}
}
?>