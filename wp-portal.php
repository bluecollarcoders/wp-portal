<?php
/**
 * Plugin Name: WP Portal
 * Plugin URI:  https://example.com
 * Description: Client Portal / Project Management plugin.
 * Version:     1.0.0
 * Author: Caleb Matteis
 * Text Domain: wp-portal
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Require the main plugin class.
require_once plugin_dir_path(__FILE__) . 'includes/classes/class-wp-portal-plugin.php';

// Activation hook (run migrations).
register_activation_hook( __FILE__, 'wp_portal_on_activate' );

function wp_portal_on_activate() {
    WP_Portal_Plugin::activate();
}

// Deactivation hook.
register_deactivation_hook( __FILE__, 'wp_portal_on_deactivate' );

function wp_portal_on_deactivate() {
    WP_Portal_Plugin::deactivate();

}

function portal_plugin_load() {
new WP_Portal_Plugin();
}
add_action( 'plugins_loaded', 'portal_plugin_load' );
