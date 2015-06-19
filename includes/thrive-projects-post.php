<?php
add_action('admin_head', 'thrive_admin_css');

function thrive_admin_css() {
	wp_enqueue_style('thrive_admin_style', plugin_dir_url(__FILE__) . '../assets/css/style.css');
}

add_action( 'init', 'codex_project_init' );
/**
 * Register a project post type.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 */
function codex_project_init() {
	$labels = array(
		'name'               => _x( 'Projects', 'post type general name', 'your-plugin-textdomain' ),
		'singular_name'      => _x( 'Project', 'post type singular name', 'your-plugin-textdomain' ),
		'menu_name'          => _x( 'Projects', 'admin menu', 'your-plugin-textdomain' ),
		'name_admin_bar'     => _x( 'Project', 'add new on admin bar', 'your-plugin-textdomain' ),
		'add_new'            => _x( 'Add New', 'project', 'your-plugin-textdomain' ),
		'add_new_item'       => __( 'Add New Project', 'your-plugin-textdomain' ),
		'new_item'           => __( 'New Project', 'your-plugin-textdomain' ),
		'edit_item'          => __( 'Edit Project', 'your-plugin-textdomain' ),
		'view_item'          => __( 'View Project', 'your-plugin-textdomain' ),
		'all_items'          => __( 'All Projects', 'your-plugin-textdomain' ),
		'search_items'       => __( 'Search Projects', 'your-plugin-textdomain' ),
		'parent_item_colon'  => __( 'Parent Projects:', 'your-plugin-textdomain' ),
		'not_found'          => __( 'No projects found.', 'your-plugin-textdomain' ),
		'not_found_in_trash' => __( 'No projects found in Trash.', 'your-plugin-textdomain' )
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
		'register_meta_box_cb'      => 'project_meta_box',
		'supports'           => array( 'title', 'editor', 'thumbnail' )
	);

	register_post_type( 'project', $args );
}


add_action('add_meta_boxes_post' ,'project_meta_box');

function project_meta_box() {

    wp_enqueue_script('jquery-ui-tabs');
    wp_enqueue_script('jquery-ui-datepicker');

    add_meta_box(
		'some_meta_box_name', 
		__( 'Contributors', 'myplugin_textdomain' ), 
		'render_meta_box_content',
		'project',
		'advanced',
		'high'
	);

	add_meta_box(
		'shit2', 
		__( 'Milestones', 'myplugin_textdomain' ), 
		'render_meta_box_content1',
		'project',
		'advanced',
		'high'
	);

	add_meta_box(
		'shit3', 
		__( 'Tasks', 'myplugin_textdomain' ), 
		'render_meta_box_content2',
		'project',
		'advanced',
		'high'
	);
}

function render_meta_box_content() {
	?>
	<div id="thrive-contributors">
		Add contributors to this project
	</div>
	<?php
}

function render_meta_box_content1() {
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

function render_meta_box_content2() {
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
				<p>
					<strong>There are no tasks to this project yet.</strong>
					Click 'Add Task' tab above to add new task for this project
				</p>
			</div>
			<div id="thrive-add-task">
				<div class="form-wrap">
					<div class="thrive-form-field">
						<input placeholder="Task Title" type="text" id="title" name="title" class="widefat"/>
						<br><span class="description"><?php _e('Enter the title of this task. Max 160 characters', 'thrive'); ?></span>
					</div><br/>
				
					<div class="thrive-form-field">
						<textarea class="widefat" rows="5" cols="100" id="description" placeholder="Description"></textarea>
						<br><span class="description"><?php _e('In few words, explain what this task is all about', 'thrive'); ?></span>
					</div><br/>

					<div class="thrive-form-field">
						<label for="thrive-milestone">Milestone:</label>
						<select id="thrive-milestone" name="thrive-milestone">
							<option>Alpha Release RC2.2</option>
							<option>Alpha Release RC2</option>
							<option>Alpha Release RC1</option>
						</select>
					</div>

					<div class="thrive-form-field">
						<button class="button button-primary button-large" style="float:right">
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
		$('#thrive-tasks').tabs({
			active: 0
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