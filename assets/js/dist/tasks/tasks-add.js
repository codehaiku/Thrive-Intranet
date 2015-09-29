$('#thrive-submit-btn').click(function(e) {

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
            nonce: thriveProjectSettings.nonce
        },

        method: 'post',

        success: function( message ) {

            // Total tasks view.
            var total_tasks = parseInt( $('.thrive-total-tasks').text().trim() );

            // Remaining tasks view
            var remaining_tasks = parseInt( $('.thrive-remaining-tasks-count').text().trim() );

            message = JSON.parse( message );

           // console.log( message ); 

            if ( message.message === 'success' ) {

                element.text('Save Task');

                element.removeAttr('disabled');

                $('#thriveTaskDescription').val('');

                $('#thriveTaskTitle').val('');
                
                ThriveProjectView.updateStats( message.stats );

                location.href = "#tasks/view/" + message.response.id;


            } else {

                $('#thrive-add-task-message').text(message.response).show().addClass('error');

                setTimeout(function() {
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
