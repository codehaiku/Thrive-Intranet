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

	public function setId($id = 0) {
		$this->id = $id;
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

		if ($priority < 1) {$priority = 1;}
		if ($priority > 3) {$priority = 3;}

		$this->priority = $priority;

		return $this;
	}

	public function getPriority($priority = 1) {

		$priority = abs($priority);
		
		if ($priority > 3 || $priority === 0) {
			$priority = 3;
		}

		$priority_collection = $this->getPriorityCollection();

		return $priority_collection["{$priority}"];
	}

	public function getPriorityCollection() {
		return array(
			'1' => apply_filters('thrive_task_priority_1_label', 'Normal'),
			'2' => apply_filters('thrive_task_priority_2_label', 'High'),
			'3' => apply_filters('thrive_task_priority_3_label', 'Critical'),
		);
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

	public function fetch($id = null, $page = 1, $priority = -1, $orderby = 'date_created', $order = 'desc') {
		// fetch all tickets if there is no id specified
		global $wpdb;

		if ($id === null) {

			// where claused
			$filters = '';
				$allowed_priority = array('1','2','3');
				if ($priority != -1 && in_array($priority, $allowed_priority)) {
					$filters = sprintf("WHERE priority = %d", $priority);
				}

			// limit claused
			$limit = THRIVE_PROJECT_LIMIT;

			// total number of task per page
			$perpage = ceil($limit);
			
			// set the current page to 1
			$currpage = ceil($page); if ($currpage <= 0) {$currpage = 1;}

			// initiate the row offset to zero
			$offset  = 0;

			// get total number of rows in the table
			$row_count_stmt = "SELECT COUNT(*) as count from {$this->model} {$filters}";			
				$row = $wpdb->get_row($row_count_stmt, OBJECT);
					$row_count = intval($row->count);

			// control the offset
			if ($currpage !== 0) {
			    $offset = $perpage * ($currpage-1);
			}

			// controls the maximum number of page
			// if user throws a page more than
			// the result has, set it to the highest
			// number of page 
			if ($offset >= $row_count) {
				$offset = $row_count - $perpage;
			}

			// minimum page is always equal to 1
			$min_page = 1;

			// maximum page is the total number of page, hence ceil(total/perpage)
			$max_page = ceil($row_count/$limit);

			$stmt = "SELECT * FROM {$this->model} {$filters} ORDER BY {$orderby} {$order} LIMIT {$perpage} OFFSET {$offset}";

			$results = $wpdb->get_results($stmt, OBJECT);
			
			if (!empty($results)) {
				
				$stats = array();
					
					$total     = $stats['total'] 		= $row_count;
					$perpage   = $stats['perpage'] 		= $perpage;
					$totalpage = $stats['total_page'] 	= ceil($total/$perpage);
					$currpage  = $stats['current_page'] = $currpage;
					$min_page  = $stats['min_page'] 	= $min_page;
					$max_page  = $stats['max_page'] 	= $max_page;

				return array(
						'stats' => $stats,
						'results' => (object)$results
					);
			}
		}

		if (!empty($id)) {

			$stmt = sprintf("SELECT * FROM {$this->model} WHERE id = {$id} order by date_created desc");

			$result = $wpdb->get_row($stmt);

			return $result;
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

		if (!empty($this->id)) {

			return ($wpdb->update($this->model, $args, array('id'=>$this->id), $format, array('%d')) === 0);
			
		} else {
			 if ($wpdb->insert($this->model, $args, $format) ) {
			 	return $wpdb->insert_id;
			 } else {
			 	return false;
			 }
		}
	}

	public function delete() {
		
		global $wpdb;

		if (0 === $this->id) {
			echo 'Model Error: ticket ID is ' . $this->id;
		} else {
			$wpdb->delete($this->model, array('id'=>$this->id), array('%d'));
		}

		return $this;
	}
}