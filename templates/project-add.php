<div id="thrive-project-add-new-form">
	
	<form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">

		<input type="hidden" name="method"  value="thrive_transactions_update_project" />
		<input type="hidden" name="action"  value="thrive_transactions_request" />
		<input type="hidden" name="no_json" value="yes" />

		<div class="thrive-form-field">

			<?php $placeholder = __('Enter the new title for this project', 'thrive'); ?>
			<label for="thrive-project-name">
				<?php _e('Project Name', 'thrive'); ?>
			</label>
			<input required placeholder="<?php echo $placeholder; ?>" type="text" name="title" id="thrive-project-name" />

		</div>

		<div class="thrive-form-field">

			<label for="thrive-project-content">
				<?php _e('Project Details', 'thrive'); ?>
			</label>

			<textarea id="thrive-project-content" name="content" rows="5" placeholder="<?php _e('Describe what this project is all about. You can edit this later.', 'thrive');?>" required ></textarea>

		</div>

			<div class="thrive-form-field">
				<label for="thrive-project-assigned-group">
					<?php _e('Assign to Group:', 'thrive'); ?>
				</label>

				<?php $current_user_groups = thrive_get_current_user_groups(); ?>

				<?php if ( !empty($current_user_groups) ) { ?>

					<select name="group_id" id="thrive-project-assigned-group">
						<?php foreach( $current_user_groups as $group ) { ?>
						<option value="<?php echo absint( $group['group_id'] ); ?>">
							<?php echo esc_html( $group['name'] ); ?>
						</option>
						<?php } ?>
					</select>
					
				<?php } ?>

			</div>

			<div class="thrive-form-field">
				<div class="alignright">
					<button id="thriveSaveProjectBtn" type="submit" class="button">
						<?php echo _e('Save Project', 'thrive'); ?>
					</button>
				</div>
				<div class="clearfix"></div>
			</div>
		</form>	
	</div>