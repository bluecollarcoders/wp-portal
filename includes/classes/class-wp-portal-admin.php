<?php

namespace WP_Portal\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class WP_Portal_Admin
 *
 * Handles admin menu and page registration.
 */
class WP_Portal_Admin {
    
    public function __construct() {
        add_action( '_admin_menu', [$this, 'register_admin_menus'] );
    }

    public function register_admin_menus(): void {
        add_menu_page(
            'Client Portal', 
            'Client Portal', 
            'manage_options', 
            'wp-portal-dashboard', 
            [$this, 'render_dashboard'], 
            'dashicons-clipboard', 
            25
        );
    }
}
