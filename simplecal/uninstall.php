<?php
// Exit if directly accessed or if uninstall not initiated by WP
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

// Only delete data if the option is set
$delete_data = get_option('simplecal_delete_data_on_uninstall', 0);
if ( ! $delete_data ) {
	return;
}

global $wpdb;

// Delete posts of custom post types
$post_types = ['simplecal_event', 'simplecal_recurring_event'];

$placeholders = implode(',', array_fill(0, count($post_types), '%s'));
$query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type IN ($placeholders)", $post_types);
$post_ids = $wpdb->get_col($query);

if (!empty($post_ids)) {
	foreach ( $post_ids as $pid ) {
		wp_delete_post( (int) $pid, true ); // true = bypass trash (permanent)
	}
}

// Remove postmeta entries that belong to the plugin, using their meta_key prefixes
$meta_prefixes = [
	'simplecal_event_',
	'simplecal_recurring_'
];

// Delete meta rows using LIKE for each prefix
foreach ( $meta_prefixes as $prefix ) {
	$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s", $prefix . '%'));
}

// Remove plugin options. This wipes options that start with 'simplecal_'.
$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'simplecal_%'));

// Remove transient options related to the plugin
$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s", '_transient_simplecal_%', '_site_transient_simplecal_%'));

?>