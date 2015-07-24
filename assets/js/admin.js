jQuery(document).ready( function($) {

		if ( typeof thriveAjaxUrl === 'undefined' ) {
			
			return;

		}

		var ajaxurl = thriveAjaxUrl;

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
					project_id: thriveTaskConfig.currentProjectId,
					user_id: thriveTaskConfig.currentUserId,
					priority: $('#thrive-task-edit-select-id').val(),
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
			});
		});
		
		var ThriveModel = Backbone.Model.extend({
			
			page: 1,
			current_page: 1,
			max_page: 1,
			min_page: 1,
			total: 0,
			total_pages: 0,
			
			initialize: function() {
				// do nothing
			},
			
			renderAddForm: function() {

				$('.thrive-tab-item-content').removeClass('active');
				$('#thrive-edit-task-list').addClass('hidden');

				$('#thrive-add-task').addClass('active');
			},
			
			renderEditForm: function(task_id) {

				$('#thrive-edit-task-list').removeClass('hidden');
				$('.thrive-tab-item-content').removeClass('active');
				$('#thrive-edit-task').addClass('active');

				$('#thriveTaskEditTitle').val('').attr('disabled', true).val('loading...');
				$('#thriveTaskEditDescription').val('').attr('disabled', true).val('loading...');
				$('#thrive-task-edit-select-id').attr('disabled', true);

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
							
								if (response.message == "success") {
									$('#thriveTaskEditTitle').val(response.task.title).removeAttr('disabled')
									$('#thriveTaskEditDescription').val(response.task.description).removeAttr('disabled');
									$('#thrive-task-edit-select-id').val(response.task.priority).removeAttr('disabled');
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
		 * Thrive Task View
		 */
		var ThriveTaskView = Backbone.View.extend({
			model: ThriveModel,
			el: 'body',
			id: 'thrive_tasks_metabox',
			priority: -1,
			search: '',
			showCompleted: 'no',
			events: {
				"click .thrive-task-tabs": 'switchView',
				"click .next-page": "nextPage",
				"click .prev-page": "prevPage",
				"click #thrive-task-search-submit": "searchTasks",
				"click .thrive-complete-ticket": 'completeTicket',
				"click .thrive-renew-task": 'renewTask',
				"change #thrive-task-filter-select": "filterByPriority",
			},
			
			switchView: function(e, targetID) {
				if (e) {
					var element = $(e.target);
				}
				$('#thrive-task-edit-tab').hide();
				$('.thrive-task-tabs').removeClass('ui-state-active');
					
				if (targetID) {
					$(targetID).addClass('ui-state-active');
				} else {
					element.parent().addClass('ui-state-active');
				}
			},
			prevPage: function(e) {

				e.preventDefault();
				var minimum_page = 1;
				
				if (this.model.get('page') > minimum_page) {

					var current_page = this.model.get('page');
					var next_page = --current_page;

					this.model.set({
						page: next_page
					});

					location.href="#tasks/page/" + this.model.get('page');
				}
				return;
			},

			nextPage: function(e) {
				
				e.preventDefault();
				
				var maximum_page = this.model.get('max_page'); 
				var current_page = this.model.get('page');

				
					if (current_page < maximum_page) {
						
						var current_page = this.model.get('page');
						var next_page = ++current_page;

							this.model.set({
								page: next_page
							});

						location.href="#tasks/page/" + this.model.get('page');
					}

				return;

			},
			
			filterByPriority: function(e){
				
				selected = e.target.value;

				var priority = [];
					priority['1']  = 'normal';
					priority['2']  = 'high';
					priority['3']  = 'critical';

				var new_priority = priority[selected];

					if (new_priority) {
						location.href = '#tasks/show/'+new_priority;
					} else {
						this.priority = -1;
						location.href = '#tasks';
					}

			},

			searchTasks: function(e){

				var keywords = $('#thrive-task-search-field').val();
				
				if (keywords.length >= 1) {
					location.href = '#tasks/search/' + encodeURIComponent(keywords);
				} else {
					location.href = '#tasks';
				}

				return;
			},

			render: function() {

				var model = ThriveModel;
				var view = this;

				$('.thrive-tab-item-content').removeClass('active');
				$('#thrive-edit-task-list').addClass('hidden');
				$('#thrive-task-list').addClass('active');

				$('#thrive-action-preloader').css('display', 'block');

				$('#thrive-task-list-canvas').css('opacity', 0.25);
				$('#thrive-tasks-filter').css('opacity', 0.25);

				if (this.search.length === 0) {
					$('#thrive-task-search-field').val('');
				} else {
					$('#thrive-task-search-field').val(this.search);
				}

				$.ajax({
					url: ajaxurl,
					method: 'get',
					data: {
						action: 'thrive_transactions_request',
						method: 'thrive_transaction_fetch_task',
						page: model.get('page'),
						project_id: thriveTaskConfig.currentProjectId,
						priority: this.priority,
						search: this.search,
						show_completed: this.showCompleted,
						id: 0
					},
					success: function(response) {
						
						var response = JSON.parse(response);
							
							if (response.task.stats) {
								
								model.set({
									max_page: response.task.stats.max_page,
									page: response.task.stats.current_page
								});
							}
							
							$('#thrive-task-list-canvas').html(response.html);
							$('#thrive-action-preloader').css('display', 'none');
							
							$('#thrive-task-list-canvas').css('opacity',1);
							$('#thrive-tasks-filter').css('opacity', 1);

							setTimeout(function(){
								$('#thrive-task-current-page-selector').val(model.get('page'));
							}, 2000);

							$('#thrive-task-filter-select').val(view.priority);
							
						return;

					},
					error: function(error, errormessage) {
						console.log(errormessage);
						$('#thrive-action-preloader').css('display', 'none');
					}
				});
			},

			completeTicket: function(e) {
				e.preventDefault();

				var item_task_id = e.currentTarget.dataset.task_id;
				var item_user_id = e.currentTarget.dataset.user_id;
				var item_row_container = $(e.currentTarget).parent().parent().parent();
					item_row_container.css('opacity', 0.25);

				$.ajax({
					url: ajaxurl,
					method: 'post',
					data: {
						action: 'thrive_transactions_request',
						method: 'thrive_transaction_complete_task',
						task_id: item_task_id,
						user_id: item_user_id
					},
					success: function(response) {
						
						var response = JSON.parse(response);
						item_row_container.css('opacity', 1);
						if (response.message === 'success' && response.task_id !== 0) {
							var item = $(e.currentTarget);
							var item_row = item.parent().parent().parent();
								item_row.addClass('completed');

								$('a', item).text('Renew Task').parent().removeAttr('data-user_id').removeClass('thrive-complete-ticket').addClass('thrive-renew-task');
						} else {
							console.log('from success');
							this.error();
						}
					},
					error: function() {
						console.log('error!!! XD XD XD');
					}
				});
			},

			renewTask: function(e) {
				
				e.preventDefault();
				var item_row_container = $(e.currentTarget).parent().parent().parent();
					item_row_container.css('opacity', 0.25);

				var current_user_id = 1;	

				var item_task_id = e.currentTarget.dataset.task_id;
					
					$.ajax({
						url: ajaxurl,
						method: 'post',
						data: {
							action: 'thrive_transactions_request',
							method: 'thrive_transaction_renew_task',
							task_id: item_task_id,
						},
						success: function(response){
							
							var response = JSON.parse(response);
							item_row_container.css('opacity', 1);
							if (response.message === 'success' && response.task_id !== 0) {
								var item = $(e.currentTarget);
								var item_row = item.parent().parent().parent();
									item_row.removeClass('completed');

									
									$('a', item).text('Complete Task').parent().attr('data-user_id', current_user_id).addClass('thrive-complete-ticket').removeClass('thrive-renew-task');
							}
						},
						error: function(){}
					});
			},

			initialize: function() {
				$('#thrive-task-current-page-selector').val(this.model.get('page'));
				$('#thrive-task-filter-select').val(this.priority);
			}
		});
		
		var ThriveTaskView = new ThriveTaskView();
		
		/**
		 * Edit Event
		 */
		var ThriveRouter = Backbone.Router.extend({

			routes: {
				"": "index",
				"tasks": "index",
				"tasks/add": "add",
				"tasks/edit/:id": "edit",
				"tasks/page/:id": "navigatePage",
				"tasks/show/:priority": "filterByPriority",
				"tasks/search/:search": "search",

				"tasks/completed": "showCompleted"
			},

			view: ThriveTaskView,

			model: ThriveModel,

			index: function() {
				// reset paging
				this.model.set({
					page: 1
				});
				// reset priority
				this.view.priority = -1;
				// reset search
				this.view.search = "";
				// reset view
				this.view.showCompleted = 'no';

				this.view.render();
			},

			navigatePage: function(__page) {
				this.model.set({page: __page});
				this.view.render();
			},

			filterByPriority: function(priority_label) {
				
				

				var priority = [];
					priority['normal']    = 1;
					priority['high']      = 2;
					priority['critical']  = 3;

				var new_priority = priority[priority_label];
				// reset paging
				this.model.set({
					page: 1
				});
				if (new_priority) {
					this.view.priority = parseInt(new_priority);
					this.view.render();
				}	
			},

			search: function(keywords) {
				
				this.view.switchView(null, '#thrive-task-completed-tab');

				this.model.set({
					page: 1
				});
				// reset priority
				this.view.priority = -1;
				this.view.search = keywords;
				this.view.render();
			},

			showCompleted: function() {
				this.view.showCompleted = "yes";
				this.view.switchView(null, '#thrive-task-completed-tab');
				
				this.view.render();
			},

			add: function() {
				this.view.switchView(null, '#thrive-task-add-tab');
				ThriveModel.renderAddForm();
			},

			edit: function(id) {
				this.view.switchView(null, '#thrive-task-edit-tab');
				$('#thrive-task-edit-tab').show();
				ThriveModel.renderEditForm(id);
				// call the post
			},
			initialize: function() {

			}
		}); 

		var ThriveRouter = new ThriveRouter();

		Backbone.history.start();
		// prevent form submission
		$('#thrive-task-search-field').keypress(function(e){
		    if ( e.which == 13 ) e.preventDefault();
		}); 
	});