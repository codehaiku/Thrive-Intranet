<?php

/**
 * Fires at the top of the members directory template file.
 *
 * @since BuddyPress (1.5.0)
 */
do_action( 'bp_before_projects_directory' ); ?>

<div id="buddypress">
	<div id="thrive-intranet-projects">
		<h1><?php _e('Projects', 'thrive'); ?></h1>
		
		<?php
			$args = array( 'post_type' => 'project', 'posts_per_page' => 10 );
			$loop = new WP_Query( $args );
		
			while ( $loop->have_posts() ) : $loop->the_post(); ?>
			<h2>
				<a href="<?php echo the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</h2>
			<?php 
			endwhile;
		?>
	</div>
</div><!-- #buddypress -->

<?php
do_action( 'bp_after_projects_directory' );
