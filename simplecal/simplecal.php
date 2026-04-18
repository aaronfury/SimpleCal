<?php
/*
Plugin Name:  SimpleCal
Plugin URI:   https://github.com/aaronfury/SimpleCal
Description:  This is a simple, free plugin for adding calendar events to WordPress using a custom post type and a widget. It is simple, and it is free.
Version:      0.2.20260417
Requires at least: 6.7
Tested up to: 6.8.1
Requires PHP: 7.4
Author:       Aaron Firouz
License:      Creative Commons Zero
License URI:  https://creativecommons.org/publicdomain/zero/1.0/
Text Domain:  simplecal
*/

spl_autoload_register('simplecal_autoloader');

function simplecal_autoloader($class_name) {
	if (false !== strpos($class_name, 'SimpleCal')) {
		$classes_dir = realpath(plugin_dir_path( __FILE__ )) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;
		$class_file = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
		require_once $classes_dir . $class_file;
	}
}

add_action('plugins_loaded', 'simplecal_init');

function simplecal_init() {
	new SimpleCal\Plugin();
	new SimpleCal\PostTypeEvent();
}

// TODO: Rework the whole shortcode thing
//add_shortcode('simplecal', [$plugin, 'render_shortcode']);

if (is_admin()) {
	new SimpleCal\Settings();
}

?>