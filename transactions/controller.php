<?php
/**
 * This file act as a middleware for every transaction
 *
 * @since  1.0
 * @author  dunhakdis
 */

// check if access directly
if (!defined('ABSPATH'))  {die();}

header('Content-Type: application/json');

add_action( 'wp_ajax_thrive_transactions_request', 'thrive_transactions_callblack' );

require_once(plugin_dir_path(__FILE__) . '../controllers/tasks.php');

/**
 * Executes the method or function requested by the client
 * @return void
 */
function thrive_transactions_callblack() {
	
	$method = filter_input(INPUT_POST, 'method', FILTER_SANITIZE_ENCODED);

	if (empty($method)) {
		// try get action
		$method = filter_input(INPUT_GET, 'method', FILTER_SANITIZE_ENCODED);
	}

	$allowed_callbacks = array(
		'thrive_transaction_add_ticket',
		'thrive_transaction_delete_ticket',
		'thrive_transaction_fetch_task',
		'thrive_transaction_edit_ticket',
		'thrive_transaction_complete_task',
		'thrive_transaction_renew_task',
		// Comments callback functions.
		'thrive_transaction_add_comment_to_ticket',
		'thrive_transaction_delete_comment'
	);

	if (function_exists($method)) {
		if (in_array($method, $allowed_callbacks)) {
			//execute the callback
			$method();
		} else {
			thrive_api_message(array(
				'message' => 'method is not listed in the callback'
			));
		}
	} else {
		thrive_api_message(array(
			'message' => 'method not allowed or method does not exists'
		));
	}
	
	thrive_api_message(array(
			'message' => 'transaction callback executed'
		));
}

function thrive_api_message($args = array()) {
	echo json_encode($args);
	die();
}

function thrive_transaction_add_ticket() {

	$task = new ThriveProjectTasksController();
	
	$task_id = $task->addTicket($_POST);

	if ($task_id) {
		thrive_api_message(array(
			'message' => 'success',
			'response' => array(
					'id' => $task_id
				)
		));
	} else {
		thrive_api_message(array(
			'message' => 'fail',
			'response' => __('There was an error trying to add this task. Title and Description fields are required or there was an unexpected error.',' thrive')
		));
	}	

	return;
}

function thrive_transaction_delete_ticket() {

	$ticket_id = (int)filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

	$ticket = new ThriveProjectTasksController();
	
	$ticket->deleteTicket($ticket_id);

	return;
}

function thrive_transaction_fetch_task() {

	$task_id = (int)filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
	$page = (int)filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
	$project_id = (int)filter_input(INPUT_GET, 'project_id', FILTER_VALIDATE_INT);
	$priority = (int)filter_input(INPUT_GET, 'priority', FILTER_VALIDATE_INT);
	$search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_URL);
	$show_completed = filter_input(INPUT_GET, 'show_completed', FILTER_SANITIZE_STRING);
	$callback_template = filter_input(INPUT_GET, 'template', FILTER_SANITIZE_STRING);
	$html_template = 'thrive_render_task';

	if (!empty($callback_template) && function_exists($callback_template)) {
		$html_template = $callback_template;
	}


	$task = new ThriveProjectTasksController();

	$args = array(
		'project_id' => $project_id,
		'id' => $task_id,
		'page' => $page,
		'priority' => $priority,
		'search' => $search,
		'show_completed' => $show_completed,
		'orderby' => 'priority',
		'order' => 'desc',
		'echo' => 'no',
	);

	$task_collection = $task->renderTasks($args);

	if (0 === $task_id) {
		$task_id = null;
		$template = $html_template($args);
	} else {
		if (!empty($callback_template)) {
			$template = $html_template($task_collection);
		}
	}

	thrive_api_message(array(
			'message' => 'success',
			'task' => $task_collection,
			'stats'=> $task_collection->stats,
			'debug' => $task_id,
			'html' => $template
		));

	return;
}

function thrive_transaction_edit_ticket() {

	$task_id = (int)filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
	$title = filter_input(INPUT_POST, 'title', FILTER_UNSAFE_RAW);
	$description = filter_input(INPUT_POST, 'description', FILTER_UNSAFE_RAW);
	$priority = filter_input(INPUT_POST, 'priority', FILTER_UNSAFE_RAW);
	$user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
	$project_id = filter_input(INPUT_POST, 'project_id', FILTER_VALIDATE_INT);

	$task = new ThriveProjectTasksController();

	$args = array(
			'title' => $title,
			'id' => $task_id,
			'description' => $description,
			'priority' => $priority,
			'user_id' => $user_id,
			'project_id' => $project_id
		);

	$json_response = array(
		'message' => 'success',
		'debug' => $task_id,
		'html' => $template
	);

	if ($task->updateTicket($task_id, $args)) {
		$json_response['message'] = 'success';
	} else {
		$json_response['message'] = 'fail';
	}

	thrive_api_message($args);

	return;
}

function thrive_transaction_complete_task() {

	$task_id = (int)filter_input(INPUT_POST, 'task_id', FILTER_VALIDATE_INT);
	$user_id = (int)filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

	$args = array(
			'message' => 'success',
			'task_id' => 0
		);
	
	$task = new ThriveProjectTasksController();

	$task_id = $task->completeTask($task_id, $user_id);

	if ($task_id) {
		$args['message'] = 'success';
		$args['task_id'] = $task_id;
	} else {
		$args['message'] = 'fail';
	}
	
	thrive_api_message($args);
	
	return;
}

function thrive_transaction_renew_task () {

	$task_id = (int)filter_input(INPUT_POST, 'task_id', FILTER_VALIDATE_INT);
	
	$args = array(
			'message' => 'success',
			'task_id' => 0
		);

	$task = new ThriveProjectTasksController();

	$task_id = $task->renewTask($task_id);

	if ($task_id) {
		$args['message'] = 'success';
		$args['task_id'] = $task_id;
	} else {
		$args['message'] = 'fail';
	}
	
	thrive_api_message($args);
}

function thrive_transaction_add_comment_to_ticket() {

	require_once plugin_dir_path(__FILE__) . '../models/comments.php';
	require_once plugin_dir_path(__FILE__) . '../models/tasks.php';

	$comment   = new ThriveComments();
	$task      = new ThriveProjectTasksModel();

	$details   = filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING);
	$ticket_id = filter_input(INPUT_POST, 'ticket_id', FILTER_VALIDATE_INT);
	$priority  = filter_input(INPUT_POST, 'priority', FILTER_VALIDATE_INT);
	$completed = filter_input(INPUT_POST, 'completed', FILTER_SANITIZE_STRING);

	// Get the current user that is logged in.
	$user_id = get_current_user_id();

	// Update the priority.
	$task->update_priority($ticket_id, $priority);

	// Prepare the comment statuses.
	$status = array(
			'no'     => 0,
			'yes'    => 1,
			'reopen' => 2
		);

	// Update the task status
	if ($completed === 'yes') {
		$task->completeTask($ticket_id, $user_id);
	}
		// Reopen task
		if ($completed === 'reopen') {
			$task->renewTask($ticket_id);
		}

	if (empty($user_id)) {
		thrive_api_message(array(
				'message' => 'fail',
			));
	}

	$new_comment = $comment->set_details( $details )
	        			   ->set_user( $user_id )
	        			   ->set_status( $status[$completed] )
	        			   ->set_ticket_id( $ticket_id )
	                       ->save();

	if ( $new_comment ) {

		$added_comment = $comment->fetch( $new_comment );

		thrive_api_message(array(
				'message' => 'success',
				'result' => thrive_comments_template($added_comment)
			));
	}                       
	
	return;
}

function thrive_transaction_delete_comment() {

	require_once plugin_dir_path(__FILE__) . '../models/comments.php';

	$comment_id = absint(filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT));
	
	if (0 === $comment_id) {	
		thrive_api_message(array(
			'message'  => 'failure',
			'response' => 'Invalid Comment ID'
		));
	}

	// Proceed.
	$comment = new ThriveComments();

	// Delete the comment and handle the result
	if ( $comment->set_id($comment_id)->set_user(get_current_user_id())->delete() ) {
		thrive_api_message(array(
			'message'  => 'success',
		));
	} else {
	// Otherwise, tell the client to throw an error
		thrive_api_message(array(
			'message' => 'failure'
		));	
	}

	return;
}
?>