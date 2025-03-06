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
			<?php the_title( sprintf( '<h2 class="entry-title default-max-width"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
		</header><!-- .entry-header -->

			<div class="entry-content">
				<?php the_excerpt(); ?>

				<!-- THIS IS WHERE THE FUN PART GOES -->
			</div><!-- .entry-content -->

		</article><!-- #post-## -->

	<?php endwhile; // end of the loop. ?>
<?php
	endif;

	get_footer();
?>