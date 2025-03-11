<?php
	get_header();
?>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
<header class="page-header alignwide">
	<h1 class="page-title">Events</h1>
</header><!-- .page-header -->
<div class="simplecal simplecal_theme1 simplecal_archive default-max-width" data-display-style="archive" data-display-past-events="true" data-display-past-events-days="0" data-display-future-events-days="0"  data-agenda-posts-per-page="10" data-agenda-show-thumbnail="true" data-agenda-show-excerpt="true" data-agenda-excerpt-lines="0">
	<div class="simplecal_events_wrapper"></div>
	<div class="simplecal_nav_pagination">
		<div class="simplecal_nav_prev">
			<div class="simplecal_nav_arrow">
				<span class="material-symbols-outlined">arrow_back</span>
			</div>
			<div>Previous Events</div>
		</div>
		<div class="simplecal_nav_next">
			<div>Future Events</div>
			<div class="simplecal_nav_arrow">
				<span class="material-symbols-outlined">arrow_forward</span>
			</div>
		</div>
	</div>
</div>
<?php
	get_footer();
?>