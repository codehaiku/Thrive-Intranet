<?php
/**
 * Fires at the top of the members directory template file.
 *
 * @since ThriveIntranet 1.0
 */
do_action( 'thrive_before_projects_directory' ); ?>

<div id="buddypress">
	
	<div id="thrive-intranet-projects">
		
		<a id="thrive-new-project-btn" class="alignright button" href="#">
			<?php _e('New Project', 'thrive'); ?>	
		</a>

		<div class="clearfix"></div>


		<div id="thrive-new-project-modal">

			<div id="thrive-modal-content">

				<div id="thrive-modal-heading">
					<h5 class="alignleft">
						<?php _e('Add New Project', 'thrive'); ?>
					</h5>
					<span id="thrive-modal-close" class="alignright">&times;</span>
					<div class="clearfix"></div>
				</div>

				<div id="thrive-modal-body">
					<?php thrive_new_project_form(); ?>
				</div>

				<div id="thrive-modal-footer">
					<small>
						<?php _e("Tip: Press the <em>'escape'</em> key in your keyboard to hide this form", 'thrive'); ?>
					</small>
				</div>
				
			</div>
			
		</div>

		<?php thrive_project_loop( array() ); ?>
		
	</div><!--#thrive-intranet-projects-->

</div><!-- #buddypress -->

<?php
do_action( 'thrive_after_projects_directory' );
