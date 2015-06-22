<?php
add_action('wp_enqueue_scripts', 'thrive_register_task_controller');
add_action('wp_footer', 'thrive_register_footer_actions');

function thrive_register_task_controller() {
	wp_enqueue_script('thrive-intranet-task-controller', plugin_dir_url(__FILE__) . '../assets/js/thrive-intranet.js', array('jquery', 'backbone'), $ver = 1.0, $in_footer = true);
	return;
}

function thrive_register_footer_actions() {
	echo '<script>var thriveAjaxUrl = "'.admin_url('admin-ajax.php').'";</script>';
}
?>