jQuery(document).ready(function($){
	
	var ThriveProjectModel = Backbone.View.extend({
		id: 0,
		project_id: thriveProjectSettings.project_id,
		page: 1,
		priority: -1,
		current_page: 1,
		max_page: 1,
		min_page: 1,
		total: 0,
		show_completed: 'no',
		total_pages: 0,
	});

	var ThriveProjectModel = new ThriveProjectModel();

	var ThriveProjectView  = Backbone.View.extend({

		el: 'body',
		model: ThriveProjectModel,
		search: '',
		template: '',
		events: {
			"click .thrive-project-tab-li-item-a": "switchView",
			"click .next-page": "next",
			"click .prev-page": "prev",
			"click #thrive-task-search-submit": "searchTasks",
			"change #thrive-task-filter-select": "filter"
		},

		switchView: function(e, elementID) {
			
			$('#thrive-project-edit-tab').css('display', 'none');
			$('#thrive-project-add-new').css('display', 'none');

			$('.thrive-project-tab-li-item').removeClass('active');
			$('.thrive-project-tab-content-item').removeClass('active');
			
			if (e) {
				var $element = $(e.currentTarget);
				var $active_content = $element.attr('data-content');
				//activate selected tab
				$element.parent().addClass('active');
				$('div[data-content='+$active_content+']').addClass('active');	
			} else {
				$(elementID).addClass('active');
				console.log(elementID);
				var $active_content = $(elementID).attr('data-content');
				$('a[data-content='+$active_content+']').parent().addClass('active');
			}
		},

		hideFilters: function() {
			$('#thrive-tasks-filter').hide();
		},

		showFilters: function() {
			$('#thrive-tasks-filter').show();
		},

		searchTasks: function() {
			var keywords = $('#thrive-task-search-field').val();
				if (keywords.length == 0) {
					location.href = '#tasks';
				} else {
					location.href = '#tasks/search/'+encodeURI(keywords);
				}
		},

		filter: function(e) {
			this.model.priority = e.currentTarget.value;
			
			var currentRoute = Backbone.history.getFragment();

			if (currentRoute != 'tasks') {
				location.href = '#tasks';
			} else {
				this.render();
			}
		},

		next: function(e) {
			e.preventDefault();
			var currPage = this.model.page;
			if (currPage < this.model.max_page) {
				this.model.page = ++currPage;
				location.href = '#tasks/page/'+this.model.page;
			}
		},

		prev: function(e) {
			e.preventDefault();
			var currPage = this.model.page;
			if (currPage > this.model.min_page) {
				this.model.page = --currPage;
				location.href = '#tasks/page/'+this.model.page;
			}
		},

		single: function(ticket_id) {
			this.progress(true);
			var __this = this;
			this.template = 'thrive_ticket_single';
			// load the task
			this.renderTask(function(response) {
				__this.progress(false);
				var response = JSON.parse(response);
				if (response.html) {
					$('#thrive-project-tasks').html(response.html);
				}
			});
		},

		showEditForm: function(task_id) {
			
			this.progress(true);
			var __this = this;

			$('.thrive-project-tab-content-item').removeClass('active');
			$('.thrive-project-tab-li-item').removeClass('active');
			$('a#thrive-project-edit-tab').css('display', 'block').parent().addClass('active');
			$('#thrive-project-edit-context').addClass('active');

			$('#thriveTaskId').attr('disabled', true).val('loading...');
			$('#thriveTaskEditTitle').attr('disabled', true).val('loading...');;
			$("#thrive-task-edit-select-id").attr('disabled', true);

			this.model.id = task_id;

			// Render the task.
			this.renderTask(function(response) {
				__this.progress(false);
				var response = JSON.parse(response);
				if (response.task) {
					var task = response.task;
						$('#thriveTaskId').val(task.id).removeAttr("disabled");
						$('#thriveTaskEditTitle').val(task.title).removeAttr("disabled");
 	 	 						tinymce.editors.thriveTaskEditDescription.setContent(task.description);
						$("#thrive-task-edit-select-id").val(task.priority).change().removeAttr("disabled");
				}
				return;
			});

		},

		renderTask: function(__callback) {
			$.ajax({
				url: ajaxurl,
				method: 'get',
				data: {
					action: 'thrive_transactions_request',
					method: 'thrive_transaction_fetch_task',
					id: this.model.id,
					template: this.template
				},
				success: function(response) {
					__callback(response);
				}
			});
		},

		render: function() {

			var __this = this;
			this.progress(true);

			$.ajax({
				url: ajaxurl,
				method: 'get',
				data: {
					action: 'thrive_transactions_request',
					method: 'thrive_transaction_fetch_task',
					id: this.model.id,
					project_id: this.model.project_id,
					page: this.model.page,
					search: this.search,
					priority: this.model.priority,
					template: 'thrive_the_tasks',
					show_completed: this.model.show_completed
				},
				success: function(response) {
					
					__this.progress(false);

					var response = JSON.parse(response);

						if (response.message == 'success') {
							if(response.task.stats) {
								// update model max_page and min_page
								ThriveProjectModel.max_page = response.task.stats.max_page;
								ThriveProjectModel.min_page = response.task.stats.min_page;
							}
							// render the result
							$('#thrive-project-tasks').html(response.html);
						}

						if ( 0 === response.task.length ) {
							$('#thrive-project-tasks').html('<div class="error" id="message"><p>No tasks found. If you\'re trying to find a task, kindly try different keywords and/or filters.</p></div>');
						}
						
				},
				error: function() {

				}
			});
		},

		initialize: function() {
			
		},

		progress: function(isShow) {
			
			if (isShow) {
				var __display = 'block';
				var __opacity = 0.25;
			} else {
				var __display = 'none';
				var __opacity = 1;
			}

			$('#thrive-preloader').css({display: __display});
			$('#thrive-project-tasks').css({opacity: __opacity});
			
			return;
		}
	});

	var ThriveProjectView = new ThriveProjectView();


	var ThriveProjectRoute = Backbone.Router.extend({
		routes: {
			"tasks": "index",
			"tasks/dashboard": "dashboard",
			"tasks/settings": "settings",
			"tasks/completed": "completed_tasks",
			"tasks/add": "add",
			"tasks/edit/:id": "edit",
			"tasks/page/:page": "next",
			"tasks/view/:id": "view_task",
			"tasks/search/:search_keyword": 'search',
		},
		view: ThriveProjectView,
		model: ThriveProjectModel,
		index: function() {
			
			this.view.switchView(null, '#thrive-project-tasks-context');

			this.model.page = 1;
			this.model.id = 0;
			this.model.show_completed = 'no';

			this.view.search = '';
			this.view.render();
		},
		
		dashboard: function() {
			this.view.switchView(null, '#thrive-project-dashboard-context');
		},
		settings: function() {
			this.view.switchView(null, '#thrive-project-settings-context');
		},
		add: function() {
			this.view.switchView(null, '#thrive-project-add-new-context');
			$('#thrive-project-add-new').css('display', 'block');
		},
		completed_tasks: function() {
			
			this.view.switchView(null, '#thrive-project-tasks-context');

			this.model.show_completed = 'yes';
			this.view.render();
		},
		edit: function(task_id) {
			this.view.showEditForm(task_id);
		},
		next: function(page) {
			this.model.page = page;
			this.view.render();
		},
		view_task: function(task_id) {
			this.model.id = task_id;
			this.view.single(task_id);
			this.view.switchView(null, '#thrive-project-tasks-context');
		},
		search: function(keywords) {
			this.model.page = 1;
			this.model.id = 0;
			this.view.search = keywords;
			this.view.render();
		}
	});

	var ThriveProjectRoute = new ThriveProjectRoute();

		ThriveProjectRoute.on('route', function(route){
			if ('view_task' === route) {
				this.view.hideFilters();
			} else {
				this.view.showFilters();
			}
		});

	Backbone.history.start();

	/**
	 * Standalone Events
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
				description: tinymce.editors.thriveTaskDescription.getContent(),
				milestone_id: $('#thriveTaskMilestone').val(),
				project_id: thriveTaskConfig.currentProjectId,
				user_id: thriveTaskConfig.currentUserId,
				priority: $('#thrive-task-priority-select').val(),
			},
			method: 'post',
			success: function(message) {
				message = JSON.parse(message);
				if (message.message === 'success') {
					
					element.text('Save Task');
					element.removeAttr('disabled');
				
					$('#thriveTaskDescription').val('');
					$('#thriveTaskTitle').val('');
					
					location.href="#tasks/view/"+message.response.id;

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
		}); // end $.ajax
	}); // end $('#thrive-submit-btn').click()

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
		 			description: tinymce.editors.thriveTaskEditDescription.getContent(),
		 			milestone_id: $('#thriveTaskMilestone').val(),
		 			id: $('#thriveTaskId').val(),
		 			project_id: thriveTaskConfig.currentProjectId,
		 			user_id: thriveTaskConfig.currentUserId,
		 			priority: $('#thrive-task-edit-select-id').val(),
		 		},
		 		method: 'post',
		 		success: function(message) {
		 			
		 			var response = JSON.parse(message);
		 			console.log(response.id);
		 			var thriveUpdatedTaskMessage = "<p>Task successfully updated <a href='#tasks/view/"+response.id+"'>&#65515; View</a></p>";

		 			$('#thrive-edit-task-message').html(thriveUpdatedTaskMessage).show();

		 			element.attr('disabled', false);
		 			element.text('Update Task');
		 		},
		 		error: function() {
		 			console.log('An Error Occured [Front-end.js]#311')
		 		}
		 	});
		 }); // end $('#thrive-edit-btn').click()
	
	/**
	 * New Comment
	 */
	$('body').on('click', '#updateTaskBtn', function(){
		
		var comment_ticket = ThriveProjectModel.id,
			comment_details = $('#task-comment-content').val(),
			task_priority = $('#thrive-task-priority-update-select').val(),
			comment_completed = $('input[name=task_commment_completed]:checked').val();
			
		if ( 0 === comment_ticket ) {
			return;
		}

		if ( 0 === comment_details.length ) {
			return;
		}

		// notify the user when submitting the comment form
		ThriveProjectView.progress(true);

		var __http_params = {
		 		action: 'thrive_transactions_request',
		 		method: 'thrive_transaction_add_comment_to_ticket',
		 		ticket_id:  comment_ticket,
		 		priority: task_priority,
		 		details: comment_details,
		 		completed: comment_completed
	 		};

		$.ajax({
		 	url: ajaxurl,
		 	data: __http_params,
	 		method: 'post',
	 		success: function(response) {

	 			var response = JSON.parse(response);

	 			ThriveProjectView.progress(false);
	 			
	 			$('#task-comment-content').val('');
	 			$('#task-lists').append(response.result);

	 			if ( "yes" === comment_completed ) {
	 				// disable old radios
	 				$('#ticketStatusInProgress').attr('disabled', true).attr('checked', false);
	 				$('#ticketStatusComplete').attr('disabled', true).attr('checked', false);
	 				$('#comment-completed-radio').addClass('hide');
	 				// enable new radios
	 				$('#ticketStatusCompleteUpdate').attr('disabled', false).attr('checked', true);
	 				$('#ticketStatusReOpenUpdate').attr('disabled', false);
	 				$('#thrive-comment-completed-radio').removeClass('hide');
	 			} 

	 			if ( "reopen" === comment_completed ) {
	 				// Enable old radios
	 				$('#ticketStatusInProgress').attr('disabled', false).attr('checked', true);
	 				$('#ticketStatusComplete').attr('disabled', false).attr('checked', false);
	 				$('#comment-completed-radio').removeClass('hide');
	 				// Disable new radios
	 				$('#ticketStatusCompleteUpdate').attr('disabled', true).attr('checked', false);
	 				$('#ticketStatusReOpenUpdate').attr('disabled', true);
	 				$('#thrive-comment-completed-radio').addClass('hide');
	 			}
	 		},
	 		error: function() {
	 			console.log('error');
	 			ThriveProjectView.progress(false);
	 		}
	 	});
	}); // end UpdateTask
	
	// Delete Comment Event.
	$('body').on('click', 'a.thrive-delete-comment', function(e) {

		e.preventDefault();
		
		// Ask the user to confirm if he/she really wanted to delete the task comment.
		var confirm_delete = confirm("Are you sure you want to delete this comment? This action is irreversible. ");

		// Exit if the user decided to cancel the task comment.
		if (!confirm_delete) {
			return false;
		}

		var $element = $(this);

		var comment_ticket = parseInt($(this).attr('data-comment-id'));

		var __http_params = {
			action: 'thrive_transactions_request',
		 	method: 'thrive_transaction_delete_comment',
		 	comment_id: comment_ticket
		};

		// Send request to server to delete the comment.
		ThriveProjectView.progress(true);
		$.ajax({
		 	url: ajaxurl,
		 	data: __http_params,
	 		method: 'post',
	 		success: function(response) {
	 			
	 			ThriveProjectView.progress(false);

	 			var response = JSON.parse(response);
	 			
	 			if (response.message == 'success') {
			 		$element.parent().parent().parent().parent().fadeOut(function(){
			 			$(this).remove();
			 		});
	 			} else {
	 				this.error();
	 			}
	 		},
	 		error: function() {
	 			ThriveProjectView.progress(false);
	 			$element.parent().append('<p class="error">Transaction Error: There was an error trying to delete this comment.</p>');
	 		}
	 	});
	}); // end Delete Comment
	
	// Delete Task Single
	$('body').on('click', '#thrive-delete-btn', function(){

		var _delete_confirm = confirm("Are you sure you want to delete this task? This action is irreversible");

		if (!_delete_confirm) {
			return;
		}

		var $element = $(this);

		var task_id = parseInt(ThriveProjectModel.id);
		
		var __http_params = {
			action: 'thrive_transactions_request',
			method: 'thrive_transaction_delete_ticket',
			id:  task_id,
		};
		ThriveProjectView.progress(true);
		$element.text('Deleting ...');
		$.ajax({
		 	url: ajaxurl,
		 	data: __http_params,
	 		method: 'post',
	 		success: function(response) {
	 			ThriveProjectView.progress(false);
	 			location.href = "#tasks";
	 			ThriveProjectView.switchView(null, '#thrive-project-tasks-context');
	 		},
	 		error: function() {
	 			ThriveProjectView.progress(false);
	 			location.href = "#tasks";
	 			ThriveProjectView.switchView(null, '#thrive-project-tasks-context');

	 		}
	 	});	
	}); // End Delete Task


	// Update Project
	$('body').on('click', '#thriveUpdateProjectBtn', function(){
		
		var element = $(this);

		var __http_params = {
			action: 'thrive_transactions_request',
			method: 'thrive_transactions_update_project',
			id: parseInt( $('#thrive-project-id').val() ), 
			title: $('#thrive-project-name').val(),
			content: tinymce.editors.thriveProjectContent.getContent(),
			group_id: parseInt( $('#thrive-project-assigned-group').val() )
		};

		element.attr('disabled', true).text('Updating ...');

		ThriveProjectView.progress(true);

		$('.thrive-project-updated').remove();

		$.ajax({
			url: ajaxurl,
			data: __http_params,
			method: 'post',
			success: function( response ) {

				var response = JSON.parse( response );

					if ( response.message === 'success' ) {
						
						ThriveProjectView.progress(false);

						element.attr('disabled', false).text('Update Project');

						element.parent().parent().prepend(
								'<div id="message" class="thrive-project-updated success updated">' +
								'<p>Project details successfully updated.</p>' +
								'</div>'
							)

						setTimeout(function(){
								$('.thrive-project-updated').fadeOut();
							}, 3000);

					} else {
						alert('save failure');
					}

				return;	
			},
			error: function() {

				alert('connection failure');
				return;

			}
		});
	}); // Project Update End.

	// Delete Project
	$('body').on('click', '#thriveDeleteProjectBtn', function() {
		

		if ( !confirm( 'Are you sure you want to delete this project? All the tickets under this project will be deleted as well. This action is undoable.' ) ) 
		{
			return;
		}

		var project_id = $('#thrive-project-id').val();
		
		var __http_params = {
			action: 'thrive_transactions_request',
			method: 'thrive_transactions_delete_project',
			    id: project_id
		}

		$( this ).text('Deleting...');

		$.ajax({
			url: ajaxurl,
			method: 'post',
			data: __http_params,
			success: function( response ) {

				var response = JSON.parse( response );

					if ( response.message == 'success' ) {
						
						window.location = response.redirect;

					} else {
						console.log( '__success_callback' );
						this.error();

					}

			},
			error: function() {
				alert('There was an error trying to delete this post. Try again later.');
			}
		});

	});
}); // end jQuery(document).ready();