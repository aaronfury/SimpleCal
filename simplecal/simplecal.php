<?php
/*
Plugin Name:  SimpleCal
Plugin URI:   https://github.com/aaronfury/SimpleCal
Description:  This is a simple, free plugin for adding calendar events to WordPress using a custom post type and a widget. It is simple, and it is free.
Version:      0.1.20250709
Requires at least: 6.7
Tested up to: 6.8.1
Requires PHP: 7.4
Author:       Aaron Firouz
License:      Creative Commons Zero
License URI:  https://creativecommons.org/publicdomain/zero/1.0/
Text Domain:  simplecal
*/

// TODO: Debugging shortcut... make sure these are commented out in the production site
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors','Off');
ini_set('error_log','php-errors.log');
error_reporting(E_ALL);*/

require_once(plugin_dir_path(__FILE__) . 'classes/simplecal.php');
//require_once(plugin_dir_path(__FILE__) . 'classes/simplecal_widget.php'); // TODO: Add back in when the widget is ready
$scplugin = new SimpleCal();

// TODO: Does this need to be outside the class definition? I can't remember why I did it this way.
add_shortcode('simplecal', [$scplugin, 'render_shortcode']);

if (is_admin()) {
	// Call the code for the settings page
	require_once(plugin_dir_path(__FILE__) . 'options.php');
}

?>