<?php
/**
 * WP Portal Admin Class
 *
 * This class handles the registration of admin menus and pages
 * for the WP Portal plugin.
 *
 * @package   WP_Portal
 * @author    Caleb Matteis
 * @license   GPL-2.0+
 * @link      https://example.com
 * @since     1.0.0
 */

namespace WP_Portal\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class WP_Portal_Admin
 *
 * Handles admin menu and page registration.
 */
class WP_Portal_Admin {

    /**
     * Constructor to initialize hooks.
     */
    public function __construct() {
        add_action( 'admin_menu', [$this, 'register_admin_menus'] );
    }

    /**
     * Registers the admin menus and submenus for the plugin.
     *
     * Creates the main Client Portal dashboard along with submenus
     * for managing clients, projects, credentials, and updates.
     *
     * @return void
     */
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

        add_submenu_page(
            'wp-portal-dashboard',
            'Clients',
            'Clients',
            'manage_options',
            'wp-portal-clients',
            [$this, 'render_clients_page']
        );

        add_submenu_page(
            'wp-portal-dashboard',
            'Projects',
            'Projects',
            'manage_options',
            'wp-portal-projects',
            [$this, 'render_projects_page']
        );

        add_submenu_page(
            'wp-portal-dashboard',
            'Credentials',
            'Credentials',
            'manage_options',
            'wp-portal-credentials',
            [$this, 'render_credentials_page']
        );

        add_submenu_page(
            'wp-portal-dashboard',
            'Updates',
            'Updates',
            'manage_options',
            'wp-portal-updates',
            [$this, 'render_updates_page']
        );
    }

    /**
     * Displays the main dashboard page.
     *
     * @return void
     */
    public function render_dashboard(): void {
        echo '<div class="wrap"><h1>Client Portal Dashboard</h1></div>';
    }

    /**
     * Displays the Clients page.
     *
     * @return void
     */
    public function render_clients_page(): void {
        echo '<div class="wrap"><h1>Clients</h1></div>';
    }

    /**
     * Displays the Projects page.
     *
     * @return void
     */
    public function render_projects_page(): void {
        echo '<div class="wrap"><h1>Projects</h1></div>';
    }

    /**
     * Displays the Credentials page.
     *
     * @return void
     */
    public function render_credentials_page(): void {
        echo '<div class="wrap"><h1>Credentials</h1></div>';
    }

    /**
     * Displays the Updates page.
     *
     * @return void
     */
    public function render_updates_page(): void {
        echo '<div class="wrap"><h1>Updates</h1></div>';
    }
}
