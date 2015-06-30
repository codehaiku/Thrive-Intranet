<?php
add_action('wp_enqueue_scripts', 'thrive_register_task_controller');
add_action('wp_footer', 'thrive_register_footer_actions');

function thrive_register_task_controller() {
	
	wp_enqueue_style('thrive-front-end-basic-styling', plugin_dir_url(__FILE__) . '../assets/css/front-end.css', array(), 1.0);
	
	if (is_admin()) {
		wp_enqueue_script('thrive-intranet-task-controller', plugin_dir_url(__FILE__) . '../assets/js/thrive-intranet.js', array('jquery', 'backbone'), $ver = 1.0, $in_footer = true);
	}
	
	if (is_singular(THRIVE_PROJECT_SLUG)) {
		wp_enqueue_script('thrive-intranet-scripts', plugin_dir_url(__FILE__) . '../assets/js/front-end.js', array('jquery', 'backbone'), $ver = 1.0, $in_footer = true);
	}

	return;
}

function thrive_register_footer_actions() {
	if (is_singular(THRIVE_PROJECT_SLUG)) {
		?>
		<script>
			<?php global $post; ?>
			var thriveAjaxUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
			var thriveTaskConfig = {
				currentProjectId: '<?php echo $post->ID; ?>',
				currentUserId: '<?php echo get_current_user_id(); ?>',
			}
		</script>
		<?php
	}
}
?>