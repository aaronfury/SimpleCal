<?php
	get_header();

	$description = get_the_archive_description();
?>

<?php if ( have_posts() ) : ?>

<header class="page-header alignwide">
	<h1 class="page-title">Events</h1>
</header><!-- .page-header -->

	<?php while ( have_posts() ) : the_post(); // standard WordPress loop. ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<div class="event-date default-max-width"><?= SimpleCal::event_get_the_date("date","both"); ?></div>
			<?php the_title( sprintf( '<div class="event-title default-max-width"><a href="%s">', esc_url( get_permalink() ) ), '</a></div>' ); ?>
			<?php
		if (!$post->simplecal_event_private_location || (($post->simplecal_event_private_location) && is_user_logged_in())) {
?>
				<div class="event-location default-max-width">
<?php
			if ($post->simplecal_event_venue_name || $post->simplecal_event_city) {
?>
					<div>
						<span class="simplecal_list_item_venue_name"><?= $post->simplecal_event_venue_name; ?></span><?php if ($post->simplecal_event_venue_name && ($post->simplecal_event_city || $post->simplecal_event_state)) {?><span class="simplecal_list_item_venue_separator">, </span><?php } ?><span class="simplecal_list_item_city"><?= $post->simplecal_event_city; ?></span><?php if ($post->simplecal_event_city && $post->simplecal_event_state) {?><span class="simplecal_list__item_city_separator">, </span><?php } ?><span class="simplecal_list_item_state"><?= $post->simplecal_event_state; ?></span>
					</div>
<?php
			}
			if ($post->simplecal_event_meeting_link) {
?>
					<div>
						<?= ($post->simplecal_event_meeting_link ? "<a href='{$post->simplecal_event_meeting_link}' target='_blank'>" : null) . $post->simplecal_event_virtual_platform . ($post->simplecal_event_meeting_link ? '</a>' : null) ?>
					</div>
<?php
			}
?>
				</div>
<?php
		}
?>
		</header><!-- .entry-header -->

			<div class="entry-content">
				<?php the_excerpt(); ?>
			</div><!-- .entry-content -->

	</article><!-- #post-## -->

	<?php endwhile; // end of the loop. ?>
	<div class="event-navigation default-max-width">
		<?php posts_nav_link(null,'Older events', 'Newer events'); ?>
	</div>
<?php
	endif;

	get_footer();
?>