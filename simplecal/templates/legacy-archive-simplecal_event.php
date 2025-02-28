<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php
wp_body_open();

if (wp_is_block_theme()) {
    block_template_part('header');
} else {
    get_header();
}

echo 'My content';

if (wp_is_block_theme()) {
    block_template_part('footer');
} else {
    get_footer();
}

wp_footer();
?>
</body>
</html>