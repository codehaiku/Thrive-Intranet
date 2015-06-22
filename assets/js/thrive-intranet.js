jQuery(document).ready(function($){

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
					project_id: 1,
					user_id: 1,
					priority: $('#thrive-task-priority-select').val(),
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
							console.log(response);
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
			events: {
				"click .next-page": "nextPage",
				"click .prev-page": "prevPage",
				"click #thrive-task-search-submit": "searchTasks",
				"change #thrive-task-filter-select": "filterByPriority",
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

				console.log(maximum_page);
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

				$('.thrive-tabs-tabs li').removeClass('ui-state-active');
				$('.thrive-tabs-tabs li:nth-child(1)').addClass('ui-state-active');

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
						priority: this.priority,
						search: this.search,
						id: 0
					},
					success: function(response) {

						var response = JSON.parse(response);
							
							if (response.task.stats) {
								console.log('ssssss');
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
							console.log(view.priority);
						return;

					},
					error: function(error, errormessage) {
						console.log(errormessage);
						$('#thrive-action-preloader').css('display', 'none');
					}
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
				"tasks/search/:search": "search"
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

				this.view.render();
			},

			navigatePage: function(__page) {
				this.model.set({page: __page});
				this.view.render();
			},

			filterByPriority: function(priority_label) {
				
				console.log(priority_label);

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
				this.model.set({
					page: 1
				});
				// reset priority
				this.view.priority = -1;
				this.view.search = keywords;
				this.view.render();
			},

			add: function() {
				ThriveModel.renderAddForm();
			},
			edit: function(id) {
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