<?php
/**
 * This file act as a middleware for every transaction
 *
 * @since  1.0
 * @author  dunhakdis
 */

// check if access directly
if (!defined('ABSPATH'))  {die();}

// check if user is is logged in
if (!is_user_logged_in()) {die();}

add_action( 'wp_ajax_thrive_transactions_request', 'thrive_transactions_callblack' );

require_once(plugin_dir_path(__FILE__) . '../controllers/thrive-project-tasks.php');

/**
 * Executes the method or function requested by the client
 * @return void
 */
function thrive_transactions_callblack() {
	
	$method = filter_input(INPUT_POST, 'method', FILTER_SANITIZE_ENCODED);

	$allowed_callbacks = array(
			'thrive_transaction_add_ticket',
			'thrive_transaction_delete_ticket'
		);

	if (function_exists($method)) {
		if (in_array($method, $allowed_callbacks)) {
			//execute the callback
			$method();
		}
	} else {
		die('method not allowed or method does not exists');
	}
	
	die('transaction callback executed ...');
}

function thrive_transaction_add_ticket() {

	$ticket = new ThriveProjectTasksController();
	$ticket->addTicket($_POST);

	return;
}

function thrive_transaction_delete_ticket() {

	$ticket_id = (int)filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

	$ticket = new ThriveProjectTasksController();
	
	$ticket->deleteTicket($ticket_id);

	return;
}
?>