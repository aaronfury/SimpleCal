<?php
function render_block_page() {
	$template = get_the_block_template_html();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php while (have_posts()) {the_post();} ?>
<?= $template; ?>
<h1>TITLE: <?php the_title(); ?>
<h2>DATE: <?php SimpleCal\Helper::event_get_the_date("date"); ?>
<main><?php the_content(); ?></main>
<?php wp_footer(); ?>
</body>
</html>
<?php
}

function render_classic_page() {
	get_header();
	while (have_posts()) {
		the_post();
		global $post;
		include(SimpleCal\Plugin::$path .'/template-parts/content-single.php');
	}
	get_footer();
}

if (wp_is_block_theme()) {
	render_block_page();
} else {
	render_classic_page();
}
?>