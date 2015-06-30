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
				var $active_content = $(elementID).attr('data-content');
				$('a[data-content='+$active_content+']').parent().addClass('active');
			}
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
			//$('#thriveTaskEditDescription').attr('disabled', true).val('loading...');;
			//tinymce.editors.thriveTaskEditDescription.setContent('loading...');
			$("#thrive-task-edit-select-id").attr('disabled', true);

			this.model.id = task_id;

			// render the task
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
					template: 'thrive_the_tasks'
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
			"tasks/add-new": "add",
			"tasks/edit/:id": "edit",
			"tasks/page/:page": "next",
			"tasks/view/:id": "view_task",
			"tasks/search/:search_keyword": 'search',
		},
		view: ThriveProjectView,
		model: ThriveProjectModel,
		index: function() {
			this.model.page = 1;
			this.model.id = 0;
			this.view.search = '';
			this.view.render();
		},
		add: function() {
			this.view.switchView(null, '#thrive-project-add-new-context');
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
				description: $('#thriveTaskDescription').val(),
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
		 		//	description: $('#thriveTaskEditDescription').val(),
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
		// notify the user when submitting the comment form
		ThriveProjectView.progress(true);
		setTimeout(function() {
			ThriveProjectView.progress(false);

			$('#task-lists').append($('#task-update-123').clone());
		}, 1000);
	});
}); // end jQuery(document).ready();