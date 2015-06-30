<?php

/**
 * Register our project admin styling
 */
add_action('admin_head', 'thrive_admin_css');
add_action('admin_print_scripts', 'thrive_register_backbone');

function thrive_register_backbone() {
	wp_enqueue_script('backbone');
	wp_enqueue_script('thrive-intranet', plugin_dir_url(__FILE__) . '../assets/js/thrive-intranet.js', array('jquery', 'backbone'), $ver = 1.0, $in_footer = true);
}

function thrive_admin_css() {
	wp_enqueue_style('thrive_admin_style', plugin_dir_url(__FILE__) . '../assets/css/style.css');
}

add_action( 'init', 'thrive_projects_register_post_type' );

/**
 * Register 'Projects' component post type
 * 
 * @return void
 */
function thrive_projects_register_post_type() {

	$labels = array(
		'name'               => _x( 'Projects', 'post type general name', 'thrive' ),
		'singular_name'      => _x( 'Project', 'post type singular name', 'thrive' ),
		'menu_name'          => _x( 'Projects', 'admin menu', 'thrive' ),
		'name_admin_bar'     => _x( 'Project', 'add new on admin bar', 'thrive' ),
		'add_new'            => _x( 'Add New', 'project', 'thrive' ),
		'add_new_item'       => __( 'Add New Project', 'thrive' ),
		'new_item'           => __( 'New Project', 'thrive' ),
		'edit_item'          => __( 'Edit Project', 'thrive' ),
		'view_item'          => __( 'View Project', 'thrive' ),
		'all_items'          => __( 'All Projects', 'thrive' ),
		'search_items'       => __( 'Search Projects', 'thrive' ),
		'parent_item_colon'  => __( 'Parent Projects:', 'thrive' ),
		'not_found'          => __( 'No projects found.', 'thrive' ),
		'not_found_in_trash' => __( 'No projects found in Trash.', 'thrive' )
	);

	$args = array(
		'menu_icon'			 => 'dashicons-analytics',
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'project' ),
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => null,
		'register_meta_box_cb'      => 'thrive_project_meta_box',
		'supports'           => array( 'title', 'editor', 'thumbnail' )
	);

	register_post_type( 'project', $args );

	return;
}


add_action('add_meta_boxes_post' ,'thrive_project_meta_box');

function thrive_project_meta_box() {

    wp_enqueue_script('jquery-ui-datepicker');

   /** add_meta_box(
		'thrive_contributors_metabox', 
		__('Contributors', 'thrive'), 
		'thrive_contributors_metabox_content',
		'project',
		'advanced',
		'high'
	);

	add_meta_box(
		'thrive_milestones_metabox', 
		__('Milestones', 'thrive'), 
		'thrive_milestones_metabox_content',
		'project',
		'advanced',
		'high'
	); **/

	add_meta_box(
		'thrive_tasks_metabox', 
		__( 'Tasks', 'thrive' ), 
		'thrive_tasks_metabox_content',
		'project',
		'advanced',
		'high'
	);
}

function thrive_contributors_metabox_content() {
	?>
	<div id="thrive-contributors">
		Assign a group for this project
	</div>
	<?php
}

function thrive_milestones_metabox_content() {
	?>
	<div id="thrive-milestones-tabs" class="thrive-tabs">
		<div class="thrive-tabs-tabs">
			<ul>
			    <li><a href="#thrive-milestones-list">All Milestones</a></li>
			    <li><a href="#thrive-milestones-add">New Milestone</a></li>
			</ul>
		</div>
		<div class="thrive-tabs-content">
			<div id="thrive-milestones-list" class="thrive-tab-item-content active">
				<ul>
					<li>General Milestone <a href="#">View</a> | <a href="#">Edit</a></li>
				</ul>
			</div>
			<div id="thrive-milestones-add" class="thrive-tab-item-content">
				<div class="form-wrap">
					<div class="thrive-form-field">
						<input placeholder="Milestone Name (e.g. Version 2.3.76)" type="text" id="title" name="title" class="widefat"/>
						<br><span class="description"><?php _e('Give your new milestone a new name. Maximum 160 characters', 'thrive'); ?></span>
					</div><br/>
				
					<div class="thrive-form-field">
						<textarea class="widefat" rows="5" cols="100" id="description" placeholder="Description"></textarea>
						<br><span class="description"><?php _e('In few words, explain what this milestone is all about', 'thrive'); ?></span>
					</div><br/>

					<div class="thrive-form-field">
						<label for="thrive-milestone">Deadline:</label>
						<input placeholder="Tentative Date" type="text" name="date" id="thrive-date-picker" />
					</div>

					<div class="thrive-form-field">
						<button class="button button-primary button-large" style="float:right">
							<?php _e('Save Milestone', 'dunhakdis'); ?>
						</button>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>	
		</div>
		<div class="thrive-clear"></div>
	</div>
	<script>
	jQuery(document).ready(function($){
		$( "#thrive-date-picker" ).datepicker();
	});
	</script>
	<?php
}

function thrive_tasks_metabox_content() {
	?>
	<div id="thrive-tasks" class="thrive-tabs">
		<div id="thrive-action-preloader" class="active">
			<span><?php _e('Loading', 'thrive'); ?> &hellip;</span>
		</div> 
		<div class="thrive-tabs-tabs">
			<ul>
			    <li id="thrive-task-list-tab" class="thrive-task-tabs ui-state-active"><a href="#tasks"><span class="dashicons dashicons-list-view"></span> Tasks List</a></li>
			    <li id="thrive-task-completed-tab" class="thrive-task-tabs"><a href="#tasks/completed"><span class="dashicons dashicons-yes"></span> Completed</a></li>
			    <li id="thrive-task-add-tab" class="thrive-task-tabs"><a href="#tasks/add"><span class="dashicons dashicons-plus"></span> New Task</a></li>
			    <li id="thrive-task-edit-tab" class="thrive-task-tabs hidden" id="thrive-edit-task-list"><a href="#thrive-edit-task">Edit Task</a></li>
			</ul>
		</div>
		<div class="thrive-tabs-content">
			<div id="thrive-task-list" class="thrive-tab-item-content active">
				<?php if (function_exists('thrive_task_filters')) { ?>
					<?php thrive_task_filters(); ?>
				<?php } ?>
				<?php if (function_exists('thrive_render_task')) {?>
					<?php thrive_render_task(); ?>
				<?php } ?>
			</div>

			<div id="thrive-add-task" class="thrive-tab-item-content">
				<?php thrive_add_task_form(); ?>
			</div><!--.#thrive-add-task-->

			<div id="thrive-edit-task" class="thrive-tab-item-content">
				<?php thrive_edit_task_form(); ?>
			</div><!--.#thrive-edit-task-->
			
		</div>
	</div>
	<script>
		<?php global $post; ?>
		var thriveAjaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
		var thriveTaskConfig = {
			currentProjectId: '<?php echo $post->ID; ?>',
			currentUserId: '<?php echo get_current_user_id(); ?>',
		}
	</script>
	<?php
}


add_action('wp', 'thrive_register_project_content_filter');

function thrive_register_project_content_filter() {
	
	global $post;

	if (is_singular('project')) {
		add_filter( 'the_content', 'thrive_project_content_filter' );
	}

	return;
}
function thrive_project_content_filter($content) {
    
    global $post;

    require_once(plugin_dir_path(__FILE__) . '../core/thrive-functions.php');

    $container = '<div id="thrive-project">';
    $container_end = '</div><!--#thrive-project-->';

	    $heading = '<div class="thrive-project-tabs">';
	    	$heading .= '<ul id="thrive-project-tab-li">';
	    		$heading .= '<li class="thrive-project-tab-li-item"><a data-content="thrive-project-activity" class="thrive-project-tab-li-item-a" href="#activity">Activity</a></li>';
	    		$heading .= '<li class="thrive-project-tab-li-item"><a data-content="thrive-project-about" class="thrive-project-tab-li-item-a" href="#about">About</a></li>';
	    		$heading .= '<li class="thrive-project-tab-li-item active"><a data-content="thrive-project-tasks" class="thrive-project-tab-li-item-a" href="#tasks">Tasks</a></li>';
	    		$heading .= '<li class="thrive-project-tab-li-item"><a data-content="thrive-project-add-new" class="thrive-project-tab-li-item-a" href="#tasks/add-new">Add New</a></li>';
	    		$heading .= '<li class="thrive-project-tab-li-item"><a data-content="thrive-project-edit" id="thrive-project-edit-tab" class="thrive-project-tab-li-item-a" href="#">Edit</a></li>';
	    		$heading .= '<li class="thrive-project-tab-li-item"><a data-content="thrive-project-settings" class="thrive-project-tab-li-item-a" href="#tasks/settings">Settings</a></li>';
	    	$heading .= '</ul>';
	    $heading .= '</div>';

	    $body  = '<div id="thrive-project-tab-content">';
	    	$body .= '<div class="thrive-project-tab-content-item" data-content="thrive-project-about" id="thrive-project-about-context">';
	    		$body .= $content;
	    	$body .= '</div>';
	    		
	    		ob_start();
	    	  		if ($post->post_type == 'project') {
			    		include_once thrive_template_dir(). '/thrive-single-project.php';
			    	}
			    $project_contents = ob_get_clean();
			
			$body .= $project_contents;

	    $body .= '</div>';

    return  $container . $heading . $body .  $container_end;
}
?>