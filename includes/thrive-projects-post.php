<?php

/**
 * Register our project admin styling
 */
add_action('admin_head', 'thrive_admin_css');
add_action('admin_print_scripts', 'thrive_register_backbone');

function thrive_register_backbone() {
	wp_enqueue_script('backbone');
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
			    <li class="ui-state-active"><a href="#tasks">Tasks List</a></li>
			    <li><a href="#tasks/add">Add Task</a></li>
			    <li class="hidden" id="thrive-edit-task-list"><a href="#thrive-edit-task">Edit Task</a></li>
			</ul>
		</div>
		<div class="thrive-tabs-content">
			<div id="thrive-task-list" class="thrive-tab-item-content active">
				<?php if (function_exists('thrive_render_task')) {?>
					<?php thrive_render_task(); ?>
				<?php } ?>
			</div>
			<div id="thrive-add-task" class="thrive-tab-item-content">
				<div class="form-wrap">
					<div id="thrive-add-task-message" class="thrive-notifier"></div>

					<div class="thrive-form-field">
						<input placeholder="Task Title" type="text" id="thriveTaskTitle" maxlength="160" name="title" class="widefat"/>
						<br><span class="description"><?php _e('Enter the title of this task. Max 160 characters', 'thrive'); ?></span>
					</div><br/>
				
					<div class="thrive-form-field">
						<textarea class="widefat" rows="5" cols="100" id="thriveTaskDescription" placeholder="Description"></textarea>
						<br><span class="description"><?php _e('In few words, explain what this task is all about', 'thrive'); ?></span>
					</div><br />

					<div class="thrive-form-field">
						<label for="thriveTaskPriority">
							<strong>Priority: </strong>
							<select>
								<option>Normal</option>
								<option>High Priority</option>
								<option>Critical</option>
							</select>
						</label>
					</div>

					<div class="thrive-form-field">
						<button id="thrive-submit-btn" class="button button-primary button-large" style="float:right">
							<?php _e('Save Task', 'dunhakdis'); ?>
						</button>
						<div style="clear:both"></div>
					</div>
				</div>
			</div><!--.#thrive-add-task-->
			<div id="thrive-edit-task" class="thrive-tab-item-content">
				<div class="form-wrap">
					<div id="thrive-edit-task-message" class="thrive-notifier"></div>

					<input type="hidden" id="thriveTaskId" />
					<div class="thrive-form-field">
						<input placeholder="Task Title" type="text" id="thriveTaskEditTitle" maxlength="160" name="title" class="widefat"/>
						<br><span class="description"><?php _e('Enter the title of this task. Max 160 characters', 'thrive'); ?></span>
					</div><br/>
				
					<div class="thrive-form-field">
						<textarea class="widefat" rows="5" cols="100" id="thriveTaskEditDescription" placeholder="Description"></textarea>
						<br><span class="description"><?php _e('In few words, explain what this task is all about', 'thrive'); ?></span>
					</div><br/>

					<div class="thrive-form-field">
						<label for="thriveEditTaskPriority">
							<strong>Priority: </strong>
							<select>
								<option>Normal</option>
								<option>High Priority</option>
								<option>Critical</option>
							</select>
						</label>
					</div>

					<div class="thrive-form-field">
						<button id="thrive-edit-btn" class="button button-primary button-large" style="float:right">
							<?php _e('Update Task', 'dunhakdis'); ?>
						</button>
						<div style="clear:both"></div>
					</div>
				</div>
			</div><!--.#thrive-edit-task-->
		</div>
	</div>
	<script>
	jQuery(document).ready(function($){
		
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

		/**
		 * Delete Event
		 */
		$('body').on('click', '.thrive-delete-ticket-btn', function(e){

			e.preventDefault();

			var element = $(this);
				element.text('Removing Ticket ...');

				element.parent().parent().parent().parent().remove();

			$.ajax({
				url: ajaxurl,
				method: 'post',
				data: {
					id: element.attr('data-ticket-id'),
					action: 'thrive_transactions_request',
					method: 'thrive_transaction_delete_ticket'
				},
				success: function(response) {
					console.log(response);
				},
				error: function(error_response, error_message) {
					console.log('Error:' + error_message);
				}
			});

			return;	
		});

		/**
		 * Edit Event
		 */
		$('#thrive-edit-btn').click(function(e){
			e.preventDefault();

			var element = $(this);
				element.attr('disabled', true);
				element.text('Loading ...');

			$.ajax({
				url: ajaxurl,
				data: {
					action: 'thrive_transactions_request',
					method: 'thrive_transaction_edit_ticket',
					title: $('#thriveTaskEditTitle').val(),
					description: $('#thriveTaskEditDescription').val(),
					milestone_id: $('#thriveTaskMilestone').val(),
					id: $('#thriveTaskId').val(),
					project_id: 1,
					user_id: 1,
					priority: 1,
				},
				method: 'post',
				success: function(message) {
					
					$('#thrive-edit-task-message').text('Task successfully updated').show();
					
					setTimeout(function(){
						$('#thrive-edit-task-message').text('').hide();
					}, 3000);
					
					element.attr('disabled', false);
					
					element.text('Update Task');
				},
				error: function() {

				}
			});
		});	
		/**
		 * Save Event
		 */
		$('#thrive-submit-btn').click(function(e){
			
			e.preventDefault();
			
			var element = $(this);
				element.attr('disabled', true);
				element.text('Loading ...');

			$.ajax({
				url: ajaxurl,
				data: {
					action: 'thrive_transactions_request',
					method: 'thrive_transaction_add_ticket',
					title: $('#thriveTaskTitle').val(),
					description: $('#thriveTaskDescription').val(),
					milestone_id: $('#thriveTaskMilestone').val(),
					project_id: 1,
					user_id: 1,
					priority: 1,
				},
				method: 'post',
				success: function(message) {
					message = JSON.parse(message);
					console.log(message);

					if (message.message === 'success') {
						
						element.text('Save Task');
							element.removeAttr('disabled');
						
						$('#thriveTaskDescription').val('');
							$('#thriveTaskTitle').val('');

						location.href="#tasks/edit/"+message.response.id;	

					} else {

						$('#thrive-add-task-message').text(message.response).show().addClass('error');
						
						setTimeout(function(){
							$('#thrive-add-task-message').text('').hide().removeClass('error');
						}, 3000);

						element.text('Save Task');
							element.removeAttr('disabled');
					}
				}, 
				error: function() {

				}
			});
		});

		/**
		 * Thrive Task View
		 */
		var ThriveTaskView = Backbone.View.extend({
			className: 'thrive-next-page',
			 

		});
		
		var ThriveModel = Backbone.Model.extend({
			initialize: function() {
				// do nothing
			},
			renderTasks: function() {
				$('.thrive-tabs-tabs li').removeClass('ui-state-active');
				$('.thrive-tabs-tabs li:nth-child(1)').addClass('ui-state-active');

				$('.thrive-tab-item-content').removeClass('active');
				$('#thrive-edit-task-list').addClass('hidden');
				$('#thrive-task-list').addClass('active');

				$('#thrive-action-preloader').css('display', 'block');

				$.ajax({
					url: ajaxurl,
					method: 'get',
					data: {
						action: 'thrive_transactions_request',
						method: 'thrive_transaction_fetch_task',
						id: 0
					},
					success: function(response) {
						var response = JSON.parse(response);
							console.log(response);
						$('#thrive-task-list').html(response.html);
						$('#thrive-action-preloader').css('display', 'none');

					},
					error: function(error, errormessage) {
						console.log(errormessage);
						$('#thrive-action-preloader').css('display', 'none');
					}
				});
			},
			renderAddForm: function() {

				$('.thrive-tabs-tabs li').removeClass('ui-state-active');
				$('.thrive-tabs-tabs li:nth-child(2)').addClass('ui-state-active');
				$('.thrive-tab-item-content').removeClass('active');
				$('#thrive-edit-task-list').addClass('hidden');

				$('#thrive-add-task').addClass('active');
			},
			renderEditForm: function(task_id) {

				$('.thrive-tabs-tabs li').removeClass('ui-state-active');
				$('.thrive-tabs-tabs li:nth-child(3)').addClass('ui-state-active');
				$('#thrive-edit-task-list').removeClass('hidden');
				$('.thrive-tab-item-content').removeClass('active');
				$('#thrive-edit-task').addClass('active');

				$('#thriveTaskEditTitle').val('').attr('disabled', true).val('loading...');
				$('#thriveTaskEditDescription').val('').attr('disabled', true).val('loading...');

				$('#thriveTaskId').val(task_id);

				$.ajax({
					url: ajaxurl,
					method: 'get',
					data: {
						action: 'thrive_transactions_request',
						method: 'thrive_transaction_fetch_task',
						id: task_id
					},
					success: function(response) {
						var response = JSON.parse(response);
							console.log(response);
								if (response.message == "success") {
									$('#thriveTaskEditTitle').val(response.task.title).removeAttr('disabled')
									$('#thriveTaskEditDescription').val(response.task.description).removeAttr('disabled');
								}
					},
					error: function(error, errormessage, error2) {
						console.log(error2);
					}
				});
			}
		});
	
		var ThriveModel = new ThriveModel();
		/**
		 * Edit Event
		 */
		var ThriveRouter = Backbone.Router.extend({
			routes: {
				"tasks": "index",
				"tasks/add": "add",
				"tasks/edit/:id": "edit"
			},
			index: function() {
				ThriveModel.renderTasks();
			},
			add: function() {
				ThriveModel.renderAddForm();
			},
			edit: function(id) {
				ThriveModel.renderEditForm(id);
				// call the post
			}
		}); 

		var ThriveRouter = new ThriveRouter();

		Backbone.history.start(); 
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