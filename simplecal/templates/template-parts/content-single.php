<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry_header alignwide">	
		<h2>
			<?php the_title(); ?>
		</h2>
		<div class="simplecal_event_meta">
			<?= SimpleCal::event_get_the_date("eventmeta2"); ?>
<?php
			if (!$post->simplecal_event_private_location || (($post->simplecal_event_private_location) && is_user_logged_in())) {
				echo SimpleCal::event_get_the_location("eventmeta1","linktext"); 
			}
?>
		</div>
	</header>
	<div class="entry-content simplecal-single-event-body">
		<?php the_content(); ?>
	</div>
</article>