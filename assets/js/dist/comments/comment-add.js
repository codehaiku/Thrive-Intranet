  $('body').on('click', '#updateTaskBtn', function() {

      var comment_ticket = ThriveProjectModel.id,
          comment_details = $('#task-comment-content').val(),
          task_priority = $('#thrive-task-priority-update-select').val(),
          comment_completed = $('input[name=task_commment_completed]:checked').val();

      if (0 === comment_ticket) {
          return;
      }

      if (0 === comment_details.length) {
          return;
      }

      // notify the user when submitting the comment form
      ThriveProjectView.progress(true);

      var __http_params = {
          action: 'thrive_transactions_request',
          method: 'thrive_transaction_add_comment_to_ticket',
          ticket_id: comment_ticket,
          priority: task_priority,
          details: comment_details,
          completed: comment_completed,
          nonce: thriveProjectSettings.nonce
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

              // Completed no. of tasks view.
              var completed_tasks = parseInt( $('#task-progress-completed-count').text().trim() );
              var total_tasks = parseInt( $('#thrive-total-tasks-count').text().trim() );
              var remaining_task = parseInt( $('.thrive-remaining-tasks-count').text().trim() );

              if ("yes" === comment_completed) {

                  // disable old radios
                  $('#ticketStatusInProgress').attr('disabled', true).attr('checked', false);
                  $('#ticketStatusComplete').attr('disabled', true).attr('checked', false);
                  $('#comment-completed-radio').addClass('hide');
                  // enable new radios
                  $('#ticketStatusCompleteUpdate').attr('disabled', false).attr('checked', true);
                  $('#ticketStatusReOpenUpdate').attr('disabled', false);
                  $('#thrive-comment-completed-radio').removeClass('hide');

                 

                  // Update the total completed tasks count for all views.
                  $('.task-progress-completed').text( completed_tasks + 1 );

                  var percentage = Math.floor( ( (completed_tasks + 1) / total_tasks ) * 100 );

                  $('.task-progress-percentage').css( 'width', percentage + '%' );
                  $('.task-progress-percentage-label span').text( percentage + '%' );

                  $('.thrive-remaining-tasks-count').text( remaining_task - 1 );
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

                  // Update the total completed tasks count for all views.
                  $('.task-progress-completed').text( completed_tasks - 1 );

                  var percentage = Math.floor( ( (completed_tasks - 1) / total_tasks ) * 100 );

                  $('.task-progress-percentage').css( 'width', percentage + '%' );
                  $('.task-progress-percentage-label span').text( percentage + '%' );

                  $('.thrive-remaining-tasks-count').text( remaining_task + 1 );

              }
              
          },
          error: function() {

              ThriveProjectView.progress(false);
          }
      });
  }); // end UpdateTask
