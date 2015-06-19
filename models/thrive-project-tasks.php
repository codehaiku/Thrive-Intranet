<?php
/**
 * ThriveProjectTasksModel 
 */
class ThriveProjectTasksModel{

	var $db = '';
	var $model = '';
	var $id = 0;
	var $title = "";
	var $description = "";
	var $user_id = 1;
	var $date = "";
	var $milestone_id = 0;
	var $priority = 0;
	var $project_id = 0;

	public function __construct() {
		global $wpdb;

		$this->model = sprintf("%sthrive_tasks", $wpdb->prefix);
	}

	public function prepare() {
		self::__construct();

		return $this;
	}

	public function setTitle($title = "") {
		$this->title = $title;

		return $this;
	}
	
	public function setDescription($description = "") {
		$this->description = $description;

		return $this;
	}

	public function setUser($user_id = 1) {
		$this->user_id = $user_id;

		return $this;
	}

	public function setDate($date = "") {
		$this->date = $date;

		return $this;
	}
	
	public function setMilestoneId($id = 0) {
		$this->milestone_id = $id;

		return $this;
	}

	public function setPriority($priority = 1) {
		
		$priority = intval($priority);

		if ($priory < 1) {$priority = 1;}
		if ($priory > 4) {$priority = 4;}

		$this->priority = $priority;

		return $this;
	}
	
	public function setProjectId($project_id = 0) {
		$this->project_id = $project_id;

		return $this;
	}

	public function showError() {
		$this->show_errors();
		$this->print_error();
		echo 'last query:' . $this->last_query;
	}

	public function fetch($id = null) {

		// fetch all tickets if there is no id specified
		global $wpdb;

		if ($id === null) {

			$stmt = sprintf("SELECT * FROM {$this->model} order by date_created desc");
			$results = $wpdb->get_results($stmt, OBJECT);

			if (!empty($results)) {
				return $results;
			}
		}
		return array();
	}

	public function save($args = array()) {

		global $wpdb;

		$args = array(
				'title' => $this->title,
				'description' => $this->description,
				'user' => $this->user_id,
				'milestone_id' => $this->milestone_id,
				'project_id' => $this->project_id,
				'priority' => $this->priority,
			);

		$format = array(
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d'
			);
		return $wpdb->insert($this->model, $args, $format);
	}

}