var ThriveProjectView = Backbone.View.extend({

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
            $('div[data-content=' + $active_content + ']').addClass('active');
        } else {

            $(elementID).addClass('active');

            var $active_content = $(elementID).attr('data-content');

            $('a[data-content=' + $active_content + ']').parent().addClass('active');
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
            location.href = '#tasks/search/' + encodeURI(keywords);
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
            location.href = '#tasks/page/' + this.model.page;
        }
    },

    prev: function(e) {
        e.preventDefault();
        var currPage = this.model.page;
        if (currPage > this.model.min_page) {
            this.model.page = --currPage;
            location.href = '#tasks/page/' + this.model.page;
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
                template: this.template,
                nonce: thriveProjectSettings.nonce
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
                show_completed: this.model.show_completed,
                nonce: thriveProjectSettings.nonce
            },
            success: function(response) {

                __this.progress(false);

                var response = JSON.parse(response);

                if (response.message == 'success') {
                    if (response.task.stats) {
                        // update model max_page and min_page
                        ThriveProjectModel.max_page = response.task.stats.max_page;
                        ThriveProjectModel.min_page = response.task.stats.min_page;
                    }
                    // render the result
                    $('#thrive-project-tasks').html(response.html);
                }

                if (0 === response.task.length) {
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

        $('#thrive-preloader').css({
            display: __display
        });
        $('#thrive-project-tasks').css({
            opacity: __opacity
        });

        return;
    }
});

var ThriveProjectView = new ThriveProjectView();
