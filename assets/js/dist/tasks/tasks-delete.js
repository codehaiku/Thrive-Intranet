 // Delete Task Single
 $('body').on('click', '#thrive-delete-btn', function() {

     var _delete_confirm = confirm("Are you sure you want to delete this task? This action is irreversible");

     if (!_delete_confirm) {
         return;
     }

     var $element = $(this);

     var task_id = parseInt(ThriveProjectModel.id);

     var __http_params = {
         action: 'thrive_transactions_request',
         method: 'thrive_transaction_delete_ticket',
         id: task_id,
         nonce: thriveProjectSettings.nonce
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
             $element.text('Delete');
         },
         error: function() {
             ThriveProjectView.progress(false);
             location.href = "#tasks";
             ThriveProjectView.switchView(null, '#thrive-project-tasks-context');
             $element.text('Delete');

         }
     });
 }); // End Delete Task
