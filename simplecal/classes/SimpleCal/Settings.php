<?php
namespace SimpleCal;

if (! defined('ABSPATH')) {
    exit;
}

/**
 * SimpleCal Settings page
 *
 * Registers options used by the plugin and provides an admin UI under Settings → SimpleCal.
 */
class Settings {
    public static $options_defaults = [
        'simplecal_slug' => 'events',
        'simplecal_custom_css' => '',
        'simplecal_auto_delete_past_events' => 0,
        'simplecal_auto_delete_past_events_days' => 30,
        'simplecal_delete_data_on_uninstall' => 0,
    ];

    public function __construct() {
        // admin only
        if (!is_admin()) {
            return;
        }

        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'settings_init']);
        add_action('plugin_action_links_' . plugin_basename(Plugin::$path . '/simplecal.php'), [$this, 'add_settings_link']);
    }

    public function add_admin_menu() {
        add_options_page(
            __('SimpleCal Settings', 'simplecal'),
            'SimpleCal',
            'manage_options',
            'simplecal',
            [$this, 'render_settings_page']
        );
    }

    public function settings_init() {
        // Register each option
        register_setting('simplecal_settings_group', 'simplecal_slug', [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_text'],
            'default' => self::$options_defaults['simplecal_slug'],
        ]);

        register_setting('simplecal_settings_group', 'simplecal_custom_css', [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_css'],
            'default' => self::$options_defaults['simplecal_custom_css'],
        ]);

        register_setting('simplecal_settings_group', 'simplecal_auto_delete_past_events', [
            'type' => 'boolean',
            'sanitize_callback' => [$this, 'sanitize_checkbox'],
            'default' => self::$options_defaults['simplecal_auto_delete_past_events'],
        ]);

        register_setting('simplecal_settings_group', 'simplecal_auto_delete_past_events_days', [
            'type' => 'integer',
            'sanitize_callback' => [$this, 'sanitize_positive_int'],
            'default' => self::$options_defaults['simplecal_auto_delete_past_events_days'],
        ]);

        register_setting('simplecal_settings_group', 'simplecal_delete_data_on_uninstall', [
            'type' => 'boolean',
            'sanitize_callback' => [$this, 'sanitize_checkbox'],
            'default' => self::$options_defaults['simplecal_delete_data_on_uninstall'],
        ]);

        // Section
        add_settings_section(
            'simplecal_main_section',
            'SimpleCal Settings',
            function () {
                echo '<p>Configure SimpleCal plugin behavior and appearance.</p>';
            },
            'simplecal'
        );

        // Fields
        add_settings_field(
            'simplecal_slug',
            'Slug for Event Posts',
            [$this, 'field_slug'],
            'simplecal',
            'simplecal_main_section'
        );

        add_settings_field(
            'simplecal_custom_css',
            'Custom CSS',
            [$this, 'field_custom_css'],
            'simplecal',
            'simplecal_main_section'
        );

        add_settings_field(
            'simplecal_auto_delete_past_events',
            'Auto-delete past events',
            [$this, 'field_auto_delete_toggle'],
            'simplecal',
            'simplecal_main_section'
        );

        add_settings_field(
            'simplecal_auto_delete_past_events_days',
            'Days until auto-delete',
            [$this, 'field_auto_delete_days'],
            'simplecal',
            'simplecal_main_section'
        );

        add_settings_field(
            'simplecal_delete_data_on_uninstall',
            'Delete plugin data on uninstall',
            [$this, 'field_delete_on_uninstall'],
            'simplecal',
            'simplecal_main_section'
        );
    }

    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=simplecal') . '">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

	// Field callbacks
    public function field_slug() {
        $value = get_option('simplecal_slug', self::$options_defaults['simplecal_slug']);
        echo '<input type="text" name="simplecal_slug" value="' . esc_attr($value) . '" class="regular-text" />';
        echo '<p class="description">The "slug" for SimpleCal event URLs. The default is "events", so the URL to an event would resemble "https://yoursite.com/events/EventTitle" and the archive would be at "https://yoursite.com/events/".</p>';
    }

    public function field_custom_css() {
        $value = get_option('simplecal_custom_css', self::$options_defaults['simplecal_custom_css']);
        echo '<textarea name="simplecal_custom_css" rows="8" class="large-text code" disabled>' . esc_textarea($value) . '</textarea>';
        echo '<p class="description"><em>Coming soon!</em> Add custom CSS applied to the calendar and single event layouts.</p>';
    }

    public function field_auto_delete_toggle() {
        $value = (int) get_option('simplecal_auto_delete_past_events', self::$options_defaults['simplecal_auto_delete_past_events']);
        echo '<label><input type="checkbox" name="simplecal_auto_delete_past_events" value="1" ' . checked(1, $value, false) . ' disabled /> Enable automatic deletion of past events</label>';
        echo '<p class="description"><em>Coming soon!</em> When enabled, SimpleCal will remove events older than the configured number of days.</p>';
    }

    public function field_auto_delete_days() {
        $value = (int) get_option('simplecal_auto_delete_past_events_days', self::$options_defaults['simplecal_auto_delete_past_events_days']);
        echo '<input type="number" min="0" name="simplecal_auto_delete_past_events_days" value="' . esc_attr($value) . '" class="small-text" disabled/>';
        echo '<p class="description"><em>Coming soon!</em> How many days after an event\'s end to wait before deleting it (only used if Auto-delete is enabled).</p>';
    }

    public function field_delete_on_uninstall() {
        $value = (int) get_option('simplecal_delete_data_on_uninstall', self::$options_defaults['simplecal_delete_data_on_uninstall']);
        echo '<label><input type="checkbox" name="simplecal_delete_data_on_uninstall" value="1" ' . checked(1, $value, false) . ' /> Remove plugin data on uninstall</label>';
        echo '<p class="description">If checked, uninstall will remove events, post meta, and SimpleCal options.</p>';
    }

	// Sanitize callbacks
    public function sanitize_text($value) {
        return sanitize_text_field($value);
    }

    public function sanitize_css($value) {
        // allow safe style but remove PHP tags and scripts — keep plain CSS text
        $clean = trim((string) $value);
        $clean = str_replace('<?', '', $clean);
        $clean = str_replace('?>', '', $clean);
        return $clean;
    }

    public function sanitize_checkbox($value) {
        return $value ? 1 : 0;
    }

    public function sanitize_positive_int($value) {
        $int = intval($value);
        if ($int < 0) {
            $int = 0;
        }
        return $int;
    }

	// Render settings page
    public function render_settings_page() {
        if (! current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>SimpleCal Settings</h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields('simplecal_settings_group');
                    do_settings_sections('simplecal');
                    submit_button();
                ?>
            </form>
        </div>
<?php
    }
}
?>