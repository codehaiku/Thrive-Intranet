<?php
/**
 * Controller for tasks
 */
require_once(plugin_dir_path(__FILE__) . '../models/thrive-project-tasks.php');

class ThriveProjectTasksController extends ThriveProjectTasksModel{

	public function __construct() {
		return $this;
	}

	public function addTicket($args = array()) {
		
		$this->setTitle($args['title'])
			 ->setDescription($args['description'])
			 ->setMilestoneId($args['milestone_id'])
			 ->setProjectId($args['project_id'])
			 ->setUser($args['user_id'])
			 ->setPriority($args['priority']);

		if ($this->prepare()->save()) {
			echo 'success save';
		} else {
			echo 'addTicket::error';
			//$this->showError();
		}

		return false;
	}

	public function deleteTicket($id = 0) {

		return false;
	}

	public function updateTicket($id = 0, $args = array()) {

		return false;
	}

	public function renderTickets($id = null) {

		return array();
	}

	public function renderTicketsByMilestone($milestone_id = 0){

		return array();
	}

	public function renderTicketsByUser($user_id = 0) {
		return array();
	}

	
	public function completeTicket($ticketID = 0){}


}
?>