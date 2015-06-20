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

		// delete the ticket
		if (0 === $id) {
			echo 'invalid id provided';
		}

		if ($this->setId($id)->prepare()->delete()){
			echo "delete: {$id} - success";
		} else {
			echo "delete: {$id} - failed";
		}

		return false;
	}

	public function updateTicket($id = 0, $args = array()) {

		$this->prepare()->delete();

		return false;
	}

	public function renderTickets($id = null) {

		return $this->prepare()->fetch($id);

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