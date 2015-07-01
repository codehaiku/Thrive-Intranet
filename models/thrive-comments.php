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
	protected $id = 0;

	/**
	 * Holds the comment details
	 * @var string
	 */
	protected $details = '';

	/**
	 * Holds the comment user id
	 * @var integer
	 */
	protected $user = 0;

	/**
	 * Holds the "date_added"
	 * @var string
	 */
	protected $date_added = '';

	/**
	 * Holds the ticket id where the comment is belong
	 * @var integer
	 */
	protected $ticket_id = 0;

	/**
	 * Reference to custom table use for comments
	 * @var string
	 */
	private $model = '';

	/**
	 * Prepare the object properties before using
	 *
	 * @return  object self
	 */
	public function __construct() {
		global $wpdb;
		$this->model = $wpdb->prefix . 'thrive_comments';
		$this->date_added = date( 'Y-m-d g:i:s' );
	}

	/**
	 * Set the ID of the comment
	 * @param integer $id The id of the comment.
	 * @throws Exception ID must not be empty.
	 */
	public function set_id($id = 0) {

		$id = absint( $id );

		if ( 0 === $id ) {
			throw new Exception( 'Model/Comments/:ID must not be empty' );
		}

		$this->id = absint( $id );

		return $this;
	}

	/**
	 * Set the comment details
	 * @param string $details The details of the comments.
	 * @throws Exception Details must not be empty.
	 */
	public function set_details($details = '') {

		if ( empty( $details ) ) {
			throw new Exception( 'Model/Comments/:details must not be empty' );
		}

		$this->details = $details;

		return $this;
	}

	/**
	 * Assign a user the the comment
	 *
	 * @param integer $user_id the user's ID.
	 * @throws Exception The current user must be logged in.
	 * @throws Exception The current user must exists in wp_users table.
	 * @throws Exception The $user_id must not be empty.
	 */
	public function set_user($user_id = 0) {

		if ( ! get_userdata( $user_id ) ) {
			throw new Exception( "Model/Comments/::user_id must not be equal to 0 'zero'" );
		}

		if ( ! is_user_logged_in() ) {
			throw new Exception( 'Model/Comments/setUser - User must be logged-in to proceed' );
		}

		if ( ! get_userdata( $user_id ) ) {
			throw new Exception( 'Model/Comments/setUser - User is not found' );
		}

		$this->user = $user_id;

		return $this;
	}

	/**
	 * Assign the comment to a ticket.
	 *
	 * @param integer $ticket_id The ID of the ticket.
	 * @throws Exception The $ticket_id must not be empty.
	 */
	public function set_ticket_id($ticket_id = 0) {

		$ticket_id = absint( $ticket_id );

		if ( 0 === $ticket_id ) {
			throw new Exception( 'Models/Comments/::ticket_id must not be empty' );
		}

		$this->ticket_id = $ticket_id;

		return $this;

	}

	/**
	 * Save the ticket comment
	 * @return boolean true if insertion of record succeed, otherwise false
	 */
	public function save() {

		if ( empty( $this->details ) ) { return false; }
		if ( empty( $this->user ) ) { return false; }
		if ( empty( $this->ticket_id ) ) { return false; }

		global $wpdb;

		$table = $this->model;

		$data = array(
				'details' => $this->details,
				'user' => $this->user,
				'ticket_id' => $this->ticket_id,
			);

		$formats = array(
				'%s', // The format for details.,
				'%d', // The format for user.
				'%d', // The format for ticket_id.
			);

		return $wpdb->insert( $table, $data, $formats ); // Db call ok.
	}

	/**
	 * Removes the comment from the table
	 *
	 * @return boolean Returns true if removing of comment is successful, otherwise, false
	 */
	public function delete() {

		if ( empty( $this->id ) ) {
			return false;
		}

		if ( $this->current_user_can_delete() ) {
		 	return wpdb->delete( $this->model, array( 'id' => $this->id, 'user' => $this->user ), array( '%d', '%d' ) );
		} else {
			return false;
		}

		return false;
	}

	/**
	 * Test if current logged-in user can delete the comment
	 *
	 * Conditions
	 *
	 * is_admin ? true
	 * current_user_id === comment->user ? true
	 *
	 * @return boolean
	 */
	public function current_user_can_delete() {

		global $wpdb;

		$comment_id = absint( $this->id );

		// If comment id is empty return false.
		if ( 0 === $comment_id ) {
			return false;
		}

		// Allow admins to delete the comment.
		if ( current_user_can( 'administrator' ) ) {
			return true;
		}

		// Only allow the same user to delete his own comment.
		$current_user_id = get_current_user_id();
		$comment_user = $wpdb->get_var( "SELECT user FROM $this->model WHERE id = $comment_id" ); // Db call ok; no-cache pass.
		$comment_user = absint( $comment_user );

		if ( ! empty( $comment_user ) ) {
			if ( $current_user_id === $comment_user ) {
				return true;
			}
		} else {
			return false;
		}

		// No conditions met? return false.
		return false;
	}
}
?>
