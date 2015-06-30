<div class="form-wrap">
	<div id="thrive-add-task-message" class="thrive-notifier"></div>

	<div class="thrive-form-field">
		<input placeholder="Task Title" type="text" id="thriveTaskTitle" maxlength="160" name="title" class="widefat"/>
		<br><span class="description"><?php _e('Enter the title of this task. Max 160 characters', 'thrive'); ?></span>
	</div><br/>
	
	<div class="thrive-form-field">
		<textarea class="widefat" rows="5" cols="100" id="thriveTaskDescription" placeholder="Description"></textarea>
		<br><span class="description"><?php _e('In few words, explain what this task is all about', 'thrive'); ?></span>
	</div><br />

	<div class="thrive-form-field">
		<label for="thrive-task-priority-select">
			<strong><?php _e('Priority:', 'thrive'); ?> </strong>
			<?php echo thrive_task_priority_select(); ?>
		</label>
	</div>

	<div class="thrive-form-field">
		<button id="thrive-submit-btn" class="button button-primary button-large" style="float:right">
			<?php _e('Save Task', 'dunhakdis'); ?>
		</button>
		<div style="clear:both"></div>
	</div>
</div>