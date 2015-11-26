$('#thrive-edit-btn').click(function(e) {

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
            nonce: thriveProjectSettings.nonce
        },

        method: 'post',

        success: function( httpResponse ) {

            var response = JSON.parse( httpResponse );

            var message = "<p class='success'>Task successfully updated <a href='#tasks/view/" + response.id + "'>&#65515; View</a></p>";

            if ('fail' === response.message && 'no_changes' !== response.type) {

                message = "<p class='error'>There was an error updating the task. All fields are required.</a></p>";

            }

            $('#thrive-edit-task-message').html(message).show();

            element.attr('disabled', false);

            element.text('Update Task');

            return;

        },
        
        error: function() {

            // Todo: Better handling of http errors and timeouts.
            console.log('An Error Occured [thrive.js]#311');

            return;
        }
    });
}); // end $('#thrive-edit-btn').click()
