<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry_header alignwide">	
		<h2>
			<?php the_title(); ?>
		</h2>
		<div class="simplecal_event_meta">
			<div class="simplecal_event_meta_row">
				<span class="simplecal_event_meta_label">Start</span>
				<span class="simplecal_event_meta_value">
					<?= SimpleCal::event_get_the_date("both","start","l M d, Y"); ?>
				</span>
			</div>
			<?php
				if (SimpleCal::event_has_valid_end_timestamp()) {
			?>
			<div class="simplecal_event_meta_row">
				<span class="simplecal_event_meta_label">End</span>
				<span class="simplecal_event_meta_value">
					<?= SimpleCal::event_get_the_date("both","end","l M d, Y"); ?>
				</span>
			</div>
			<?php
				}
			
				if (!$post->simplecal_event_private_location || (($post->simplecal_event_private_location) && is_user_logged_in())) {
			?>
			<div class="simplecal_event_meta_row">
				<span class="simplecal_event_meta_label">Location</span>
				<span class="simplecal_event_meta_value"><?= SimpleCal::event_get_the_location(link_type:'after'); ?></span>
			</div>
			<?php
				}

				if ($post->simplecal_event_website) {
			?>
			<div class="simplecal_event_meta_row">
			 	<span class="simplecal_event_meta_label">Website</span>
				<span class="simplecal_event_meta_value"><?= SimpleCal::get_formatted_website($post->simplecal_event_website); ?></span>
			</div>
			<?php
				}
			?>
		</div>
	</header>
	<div class="entry-content simplecal-single-event-body">
		<?php the_content(); ?>
	</div>
</article>