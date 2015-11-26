var __ThriveProjectRoute = Backbone.Router.extend({
    
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
        
        if ( tinymce.editors.thriveTaskDescription ) {
            tinymce.editors.thriveTaskDescription.setContent('');
        }
    },
    completed_tasks: function() {

        this.view.switchView(null, '#thrive-project-tasks-context');

        this.model.show_completed = 'yes';
        this.view.render();
    },
    edit: function(task_id) {
        
        this.view.showEditForm(task_id);

        $('#thrive-edit-task-message').html('');

        if ( tinymce.editors.thriveTaskEditDescription ) {
            tinymce.editors.thriveTaskEditDescription.setContent('');
        }
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

var ThriveProjectRoute = new __ThriveProjectRoute();

ThriveProjectRoute.on('route', function(route) {
    if ('view_task' === route) {
        this.view.hideFilters();
    } else {
        this.view.showFilters();
    }
});

Backbone.history.start();
