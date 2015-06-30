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

function thrive_count_tasks($project_id, $type = 'all') {

	require_once(plugin_dir_path(__FILE__) . '../controllers/thrive-project-tasks.php');
	
	$thrive_tasks = new ThriveProjectTasksModel();

	return $thrive_tasks->getCount($project_id, $type);

}

function thrive_add_task_form() {
	include plugin_dir_path(__FILE__) . '../templates/add-task.php';
}

function thrive_edit_task_form() {
	include plugin_dir_path(__FILE__) . '../templates/edit-task.php';
}

function thrive_task_filters() {
	?>
	<div id="thrive-tasks-filter">
		<div class="alignleft">
			<select name="thrive-task-filter-select-action" id="thrive-task-filter-select">
				<option value="-1" selected="selected"><?php _e('Show All', 'thrive'); ?></option>
				<option value="1"><?php _e('Normal Priority', 'thrive'); ?></option>
				<option value="2"><?php _e('High Priority', 'thrive'); ?></option>
				<option value="3"><?php _e('Critical Priority', 'thrive'); ?></option>
			</select>
		</div><!--.alignleft actions bulkactions-->

		<div class="alignright">
			<p class="thrive-search-box">
				<label class="screen-reader-text">
					<?php _e('Search Tasks:', 'thrive'); ?>
				</label>
				<input maxlength="160" placeholder="<?php _e('Search Task', 'thrive'); ?>" type="search" id="thrive-task-search-field" name="thrive-task-search" value="">
				<input type="button" id="thrive-task-search-submit" class="button" value="<?php _e('Apply', 'thrive'); ?>">
			</p><!--.search box-->
		</div>

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


function thrive_render_task($args = array()) {
	
	$defaults = array(
			'project_id' => 0,
			'page' => 1,
			'priority' => -1,
			'search' => '',
			'orderby' => 'date_created',
			'order' => 'desc',
			'show_completed' => 'no',
			'echo' => true
		);

	foreach ($defaults as $option => $value) {
		
		if (!empty($args[$option])) {
			$$option = $args[$option];
		} else {
			$$option = $value;
		}
	}
	// todo convert thrive_render_task params to array

	if ($echo === 'no') { ob_start(); }

	require_once(plugin_dir_path(__FILE__) . '../controllers/thrive-project-tasks.php');

	$thrive_tasks = new ThriveProjectTasksController();
	$tasks = $thrive_tasks->renderTasks($args);
	$stats = $tasks['stats'];
	$tasks = $tasks['results'];
	$current_user_id = get_current_user_id();

	echo '<div id="thrive-task-list-canvas">';	  

	$open_tasks_no     = thrive_count_tasks($project_id, $type = 'open');
	$completed_task_no = thrive_count_tasks($project_id, $type = 'completed');
	$all_tasks_no      = thrive_count_tasks($project_id, $type = 'all');


	if (!empty($search)) {
		echo '<p id="thrive-view-info">'.sprintf(__('Search result for: "%s"'), esc_html($search)).'</p>';
	} else {
		if ($show_completed == 'no') {
			echo '<p id="thrive-view-info">'.sprintf(_n('Currently showing %d task ', 'Currently showing %d tasks ', $open_tasks_no, 'thrive'), $open_tasks_no);
			echo sprintf(__('out of %d', 'thrive'), $all_tasks_no) . '</p>';
		}

		if ($show_completed == 'yes') {
			echo '<p id="thrive-view-info">'.sprintf(_n('Currently showing %d completed task ', 'Currently showing %d completed tasks ', $completed_task_no, 'thrive'), $completed_task_no);
			echo sprintf(__('out of %d', 'thrive'), $all_tasks_no) . '</p>';
		}
	}

	if (empty($tasks)) {
		
		echo '<p class="bp-template-notice error" id="thrive-message">';
			echo __('No results found. Try another filter or add new task.', 'thrive');
		echo '</p>';

	} else {

		
		echo '<table class="wp-list-table widefat fixed striped pages" id="thrive-core-functions-render-task">';
		echo '<tr>';
			echo '<th width="70%">'.__('Title', 'thrive').'</th>';
			echo '<th>'.__('Priority', 'thrive').'</th>';
			echo '<th>'.__('Date', 'thrive').'</th>';
		echo '</tr>';
		
		foreach((array)$tasks as $task) {
			
			$priority_label = $thrive_tasks->getPriority($task->priority);
			
			$completed = '';
			if ($task->completed_by != 0) {
				$completed = 'completed';
			}
			
			$classes = implode(' ', array(esc_attr(sanitize_title($priority_label)), $completed));

			$row_actions = '<div class="row-actions">';
				$row_actions .= '<span class="edit"><a href="#tasks/edit/'.intval($task->id).'">Edit</a> | </span>';
				if (empty($completed)) {
					$row_actions .= '<span data-user_id="'.intval($current_user_id).'" data-task_id="'.intval($task->id).'" class="thrive-complete-ticket"><a href="#">Complete</a> | </span>';
				} else {
					$row_actions .= '<span data-task_id="'.intval($task->id).'" class="thrive-renew-task"><a href="#">Renew Task</a> | </span>';
				}
				$row_actions .= '<span class="trash"><a data-ticket-id="'.intval($task->id).'" class="thrive-delete-ticket-btn" href="#">Delete</a> </span>';
			$row_actions .= '</div>';

			echo '<tr class="'.$classes.'">';
				echo '<td><strong><a class="row-title" href="#tasks/edit/'.intval($task->id).'">'. stripslashes(esc_html($task->title)).'</a></strong>'.$row_actions.'</td>';
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
	}

	echo '</div><!--#thrive-task-list-canvas-->';

	if ($echo === 'no') { 
		return ob_get_clean(); 
	} else {
		return;
	}
}

/**
 * Renders the tasks
 * @param  [type] $args [description]
 * @return [type]       [description]
 */
function thrive_the_tasks($args) {

	ob_start();

	require_once(plugin_dir_path(__FILE__) . '../controllers/thrive-project-tasks.php');

	$defaults = array(
			'project_id' => 0,
			'page' => 1,
			'priority' => -1,
			'search' => '',
			'orderby' => 'date_created',
			'order' => 'desc',
			'show_completed' => 'no',
			'echo' => true
		);

	foreach ($defaults as $option => $value) {

		if (!empty($args[$option])) {
			$$option = $args[$option];
		} else {
			$$option = $value;
		}
	}

	$thrive_tasks = new ThriveProjectTasksController();
	$tasks = $thrive_tasks->renderTasks($args);
?>
<div class="clearfix"></div>
<div id="thrive-project-tasks">
	<?php if (!empty($tasks['results'])) { ?>
		<ul>
		<?php foreach ($tasks['results'] as $task) { ?>
			<?php
			$priority_label = $thrive_tasks->getPriority($task->priority);
			$completed = '';
			if ($task->completed_by != 0) {
				$completed = 'completed';
			}
			$classes = implode(' ', array(esc_attr(sanitize_title($priority_label)), $completed));
			?>
			<li class="<?php echo esc_attr($classes); ?>">
				<h3>
					<a href="#tasks/view/<?php echo intval($task->id); ?>">
						<span class="task-id">#<?php echo intval($task->id) ;?></span> - 
						<?php echo esc_html(stripslashes($task->title)); ?>
						<span class="task-user pull-right">
						&#65515; 
						<?php echo get_avatar(intval($task->user), 18); ?>
							<?php $user = get_userdata($task->user); ?>
								<?php echo esc_html($user->display_name); ?>
						</span>
					</a>
				</h3>
			</li>
		<?php } ?>
		</ul>
	<?php } ?>

<?php
$stats = $tasks['stats'];
$total = intval($stats['total']);
$perpage    = intval($stats['perpage']);
$total_page = intval($stats['total_page']);
$currpage   = intval($stats['current_page']);
$min_page	= intval($stats['min_page']);
$max_page   = intval($stats['max_page']);

echo '<div class="tablenav"><div class="tablenav-pages">';
echo '<span class="displaying-num">'.sprintf(_n('%s task', '%s tasks', $total, 'thrive'),$total).'</span>';
		
if ($total_page >= 1) {
	echo '<span id="thrive-task-paging" class="pagination-links">';
		echo '<a class="first-page disabled" title="'.__('Go to the first page', 'thrive').'" href="#tasks/page/'.$min_page.'">«</a>';
		echo '<a class="prev-page disabled" title="'.__('Go to the previous page', 'thrive').'" href="#">‹</a>';
			echo '<span class="paging-input"><label for="thrive-task-current-page-selector" class="screen-reader-text">'.__('Select Page', 'thrive').'</label>';
			echo '<input readonly class="current-page" id="thrive-task-current-page-selector" type="text" maxlength="'.strlen($total_page).'" size="'.strlen($total_page).'"value="'.intval($currpage).'">';
			echo ' of <span class="total-pages">'.$total_page.'</span></span>';

			echo '<a class="next-page" title="'.__('Go to the next page', 'thrive').'" href="#">›</a>';
			echo '<a class="last-page" title="'.__('Go to the last page', 'trive').'" href="#tasks/page/'.$max_page.'">»</a></span>';
		echo '</span>';
}
?>
</div><!--.tablenav-->
</div><!--.tablenav-pages -->
</div><!--#thrive-project-tasks-->
<?php
return ob_get_clean();
}

function thrive_ticket_single($task) {
	ob_start(); ?>
	<div id="thrive-single-task">
		<h2 class="h3">
			<?php echo esc_html($task->title); ?>
			<span class="edit-task">
				<a href="#tasks/edit/<?php echo intval($task->id); ?>">
					&#65515; Edit
				</a>
			</span>
		</h2>

		<ul id="task-lists">
			<li class="task-lists-item" id="task-update-123">
				<div class="task-item-update">
					<div class="task-update-owner">
						<img src="http://localhost/dunhakdis/wp-content/uploads/avatars/25/56883db478ea3307a3d801272d29967c-bpthumb.png" width="64"/>
					</div>
					<div class="task-update-details">
						<div class="task-meta">
							<p>
								<?php _e('Opened by', 'thrive'); ?> <a href="#">Keith Thorman </a>
									&middot; 
								<?php _e('Status:', 'thrive'); ?> Closed
									&middot; 
								<?php _e('Priority:', 'thrive'); ?> Critical
									&middot; 
								<?php _e('Added On:', 'thrive'); ?> February 28, 1990
							</p>
						</div>

						<div class="task-content">
							<?php echo do_shortcode($task->description); ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div><!--task-item-update-->
			</li>
		</ul><!--#task-lists-->

		<div id="task-editor">
			<div id="task-editor_update-status">
					<div class="alignleft">
						<label for="ticketStatusClosed"> 
							<input id="ticketStatusClosed" type="radio" value="closed" name="status">
							<small><?php _e('In Progress', 'thrive'); ?></small>
						</label>
					</div>
					<div class="alignleft">
						<label for="ticketStatusComplete">
							<input id="ticketStatusComplete" type="radio" value="closed" name="status">
							<small><?php _e('Complete', 'thrive'); ?></small>
						</label>
					</div>
				<div class="clearfix"></div>	
			</div>
			
			<div id="task-editor_update-content">
				<textarea id="task-comment-content" rows="5" width="100"></textarea>
			</div>

			<div id="task-editor_update-priority">
				<label for="thrive-task-priority-select">
					<?php _e('Priority', 'thrive'); ?>
					<?php thrive_task_priority_select($select = 1, $name = "thrive-task-priority-update-select", $id = 'thrive-task-priority-update-select') ;?>
				</label>
			</div>
			
			<div id="task-editor_update-submit">
				<button type="button" id="updateTaskBtn" class="button">
					<?php _e('Update Task', 'thrive'); ?>
				</button>
			</div>
		</div>
	</div>
	<?php 
	return ob_get_clean();
}
?>