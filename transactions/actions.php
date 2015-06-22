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

require_once(plugin_dir_path(__FILE__) . '../controllers/thrive-project-tasks.php');

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
			'thrive_transaction_edit_ticket'
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
	echo json_encode($args, JSON_HEX_QUOT);
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
	$limit = 5;

	$task = new ThriveProjectTasksController();

	if (0 === $task_id) {
		$task_id = null;
		$template = thrive_render_task(false, $page);
	} else {
		$template = null;
	}

	$task_collection = $task->renderTasks($task_id, $page);

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

	$task = new ThriveProjectTasksController();

	$args = array(
			'title' => $title,
			'id' => $id,
			'description' => $description,
			'priority' => $priority
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
?>