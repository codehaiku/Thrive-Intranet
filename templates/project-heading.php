<?php global $post; ?>
<div id="thrive-single-project-group-details">
	<div id="thrive-intranet-projects">
		<ul id="thrive-projects-lists">
			<li class="type-project">
				<?php thrive_project_meta( absint($post->ID) ); ?>
				<?php thrive_project_user( $post->post_author, $post->ID ); ?>
			</li>
		</ul>
	</div>
</div>