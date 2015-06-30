<?php global $post; ?>

<div class="thrive-project-tab-content-item active" data-content="thrive-project-tasks" id="thrive-project-tasks-context">

	<div id="thrive-preloader">
		<div class="la-ball-clip-rotate la-sm">
		    <div></div>
		</div>
	</div>
	
	<?php $args = array('project_id' => $post->ID); ?>
	
	<?php thrive_task_filters(); ?>

	<?php echo thrive_the_tasks($args); ?>
</div>

<div class="thrive-project-tab-content-item" data-content="thrive-project-add-new" id="thrive-project-add-new-context">
	<?php thrive_add_task_form(); ?>
</div>

<div class="thrive-project-tab-content-item" id="thrive-project-edit-context">
	<?php thrive_edit_task_form(); ?>
</div>
<script>
var thriveProjectSettings = {
	project_id: '<?php echo $post->ID;?>'
};
</script>