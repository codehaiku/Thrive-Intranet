<?php
/**
 * Thrive Comments
 *
 * @package  ThriveIntranet
 */

/**
 * Thrive Comments Object
 *
 * The structure of our comments object
 */
class ThriveComments {

	/**
	 * Holds the commend id
	 * @var integer
	 */
	var $id = 0;

	/**
	 * Holds the comment details
	 * @var string
	 */
	var $details = '';

	/**
	 * Holds the comment user id
	 * @var integer
	 */
	var $user = 0;

	/**
	 * Holds the "date_added"
	 * @var string
	 */
	var $date_added = '';

	/**
	 * Holds the ticket id where the comment is belong
	 * @var integer
	 */
	var $ticket_id = 0;

	/**
	 * Reference to custom table use for comments
	 * @var string
	 */
	var $model = '';

	/**
	 * Prepare the object properties before using
	 *
	 * @return  object self
	 */
	public function __construct() {
		global $wpdb;
		$this->model = $wpdb->prefix . 'thrive_comments';
	}

	/**
	 * Set the ID of the comment
	 * throws an error when there is no id
	 * @param integer $id the id of the comment
	 */
	public function setId($id = 0) {

		$id = intval($id);

		if ($id === 0) {
			throw new Exception("Model/Comments/:ID must not be empty");
		}

		$this->id = intval($id);

		return $this;
	}

	/**
	 * Set the comment details
	 * Throws exception if $details is empty
	 * @param string $details The details of the comments
	 */
	public function setDetails($details = "") {

		if (empty($details)) {
			throw new Exception("Model/Comments/:details must not be empty");
		}

		$this->details = $details;

		return $this;
	}

	/**
	 * Assign a user the the comment
	 * Throws exception if current user is not logged in
	 * Throws exception if current user is not found
	 * Throws exception if $user_id is empty or equal to 0 'zero'
	 * 
	 * @param integer $user_id [description]
	 */
	public function setUser($user_id = 0) {

	}
}

/**
 * Testing
 */
include '../../../../wp-load.php';
$thriveComments = new ThriveComments();

$thriveComments->setId(1)
			   ->setDetails("Hello, how are you?");


print_r($thriveComments);

echo "\n\n";
?>
