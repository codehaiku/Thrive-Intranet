<?php global $post; ?>

<div id="thrive-preloader">
	<div class="la-ball-clip-rotate la-sm">
	    <div></div>
	</div>
</div>

<div class="thrive-project-tab-content-item" data-content="thrive-project-dashboard" id="thrive-project-dashboard-context">
	
	<div id="thrive-dashboard-about">
		<h3>
			<?php _e('About', 'thrive'); ?>
		</h3>
		
		<?php echo $content; ?>
		
		<div class="clearfix"></div>

	</div><!--#thrive-dashboard-about-->

	<div id="thrive-dashboard-at-a-glance">
		<?php 
		// Total tasks.
		$total     = intval( thrive_count_tasks( $post->ID ) ); 

		// Completed tasks.
		$completed = intval( thrive_count_tasks( $post->ID, $type = 'completed' ) );

		// Remaining Tasks.
		$remaining = absint( $total - $completed );
		?>
		<h3>
			<?php _e('At a Glance', 'thrive'); ?>
		</h3>
		<ul>
			<li>
				<div class="thrive-dashboard-at-a-glance-box">
					<h4><?php printf('%d', $total); ?></h4>
					<p><?php _e('Total Tasks', 'thrive'); ?></p>
				</div>
			</li>
			<li>
				<a href="#tasks" class="thrive-dashboard-at-a-glance-box">
					<h4><?php printf('%d', $remaining); ?></h4>
					<p><?php _e('Tasks Left', 'thrive'); ?></p>
				</a>
			</li>
			<li>
				<a href="#tasks/completed" class="thrive-dashboard-at-a-glance-box">
					<h4><?php printf('%d', $completed); ?></h4>
					<p><?php _e('Tasks Completed', 'thrive'); ?></p>
				</a>
			</li>
			
		</ul>	
		<div class="clearfix"></div>
	</div><!--#thrive-dashboard-at-a-glance-->
</div>

<div class="thrive-project-tab-content-item active" data-content="thrive-project-tasks" id="thrive-project-tasks-context">
	
	<?php
		$args = array(
				'project_id' => $post->ID, 
				'orderby' => 'priority',
				'order' => 'desc'
			);
	?>
	
	<?php thrive_task_filters(); ?>
	
	<?php echo thrive_the_tasks( $args ); ?>

</div><!--#thrive-project-tasks-context-->

<div class="thrive-project-tab-content-item" data-content="thrive-project-settings" id="thrive-project-settings-context">
	<?php thrive_project_settings(); ?>
</div>

<div class="thrive-project-tab-content-item" data-content="thrive-project-add-new" id="thrive-project-add-new-context">
	<?php thrive_add_task_form(); ?>
</div>

<div class="thrive-project-tab-content-item" id="thrive-project-edit-context">
	<?php thrive_edit_task_form(); ?>
</div>

<script>
var thriveProjectSettings = {
	project_id: '<?php echo absint($post->ID);?>'
};
</script>