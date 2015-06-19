<?php

/**
 * Register our project admin styling
 */
add_action('admin_head', 'thrive_admin_css');

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
		'has_archive'        => true,
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

    wp_enqueue_script('jquery-ui-tabs');
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
			<div id="thrive-milestones-list" class="active">
				<ul>
					<li>General Milestone <a href="#">View</a> | <a href="#">Edit</a></li>
				</ul>
			</div>
			<div id="thrive-milestones-add">
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
		$('#thrive-milestones-tabs').tabs({
			active: 0
		});
		$( "#thrive-date-picker" ).datepicker();
	});
	</script>
	<?php
}

function thrive_tasks_metabox_content() {
	?>
	<div id="thrive-tasks" class="thrive-tabs">
		<div class="thrive-tabs-tabs">
			<ul>
			    <li><a href="#thrive-task-list">Tasks List</a></li>
			    <li><a href="#thrive-add-task">Add Task</a></li>
			</ul>
		</div>
		<div class="thrive-tabs-content">
			<div id="thrive-task-list" class="active">
				<?php if (function_exists('thrive_render_task')) {?>
					<?php thrive_render_task(); ?>
				<?php } ?>
			</div>
			<div id="thrive-add-task">

				<div class="form-wrap">
					<div id="thrive-add-task-message" class="thrive-notifier"></div>

					<div class="thrive-form-field">
						<input placeholder="Task Title" type="text" id="thriveTaskTitle" name="title" class="widefat"/>
						<br><span class="description"><?php _e('Enter the title of this task. Max 160 characters', 'thrive'); ?></span>
					</div><br/>
				
					<div class="thrive-form-field">
						<textarea class="widefat" rows="5" cols="100" id="thriveTaskDescription" placeholder="Description"></textarea>
						<br><span class="description"><?php _e('In few words, explain what this task is all about', 'thrive'); ?></span>
					</div><br/>

					<div class="thrive-form-field">
						<label for="thrive-milestone">Milestone:</label>
						<select id="thriveTaskMilestone" name="thrive-milestone">
							<option value="1">Alpha Release RC2.2</option>
							<option value="2">Alpha Release RC2</option>
							<option value="3">Alpha Release RC1</option>
						</select>
					</div>

					<div class="thrive-form-field">
						<button id="thrive-submit-btn" class="button button-primary button-large" style="float:right">
							<?php _e('Save Task', 'dunhakdis'); ?>
						</button>
						<div style="clear:both"></div>
					</div>
				</div>
			</div>	
		</div>
	</div>
	<script>
	jQuery(document).ready(function($){
		
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

		$('#thrive-tasks').tabs({
			active: 0
		});

		$('#thrive-submit-btn').click(function(e){
			
			e.preventDefault();
			
			var element = $(this);
				element.attr('disabled', true);
				element.text('Loading ...');

			$.ajax({
				url: ajaxurl,
				data: {
					action: 'thrive_transactions_request',
					title: $('#thriveTaskTitle').val(),
					description: $('#thriveTaskDescription').val(),
					milestone_id: $('#thriveTaskMilestone').val(),
					project_id: 1,
					user_id: 1,
					priority: 1,
				},
				method: 'post',
				success: function(message) {
					element.text('Save Task');
					element.removeAttr('disabled');
					$('#thrive-add-task-message').text('New ticket sucessfully added (edit)');
					console.log('success...');
						console.log(message);
				}, 
				error: function() {

				}
			});
		});
	});
	</script>
	<?php
}

function get_custom_post_type_template($content) {
     ob_start();
     global $post;

     if ($post->post_type == 'project') {
          include_once thrive_template_dir(). '/thrive-single-project.php';
     }
     $content .= ob_get_clean();
     return $content;
}

add_filter( 'the_content', 'get_custom_post_type_template' );
?>