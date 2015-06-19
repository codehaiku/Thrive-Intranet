<?php
add_action( 'wp_ajax_thrive_transactions_request', 'thrive_transactions_callblack' );

require_once(plugin_dir_path(__FILE__) . '../controllers/thrive-project-tasks.php');

function thrive_transactions_callblack() {
	
	$ticket = new ThriveProjectTasksController();
	$ticket->addTicket($_POST);

	die();
}
?>