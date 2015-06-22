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

		if (empty($this->title) || empty($this->description)) {
			return false;
		}

		return $this->prepare()->save();

	}

	public function deleteTicket($id = 0) {

		// delete the ticket
		if (0 === $id) {
			echo 'invalid id provided';
		}

		return $this->setId($id)->prepare()->delete();

	}

	public function updateTicket($id = 0, $args = array()) {

		$this->setTitle($args['title']);
		$this->setId($id);
		$this->setDescription($args['description']);

		return $this->prepare()->save();

	}

	public function renderTasks($id = null, $page = 1) {
		
		return $this->prepare()->fetch($id, $page);

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